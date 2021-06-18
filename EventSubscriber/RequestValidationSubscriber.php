<?php

namespace Yc\RequestValidationBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Yc\RequestValidationBundle\Attributes\RequestValidator;
use Yc\RequestValidationBundle\DataReceiver\DataReceiverInterface;
use Yc\RequestValidationBundle\DataTransformer\DataTransformerInterface;
use Yc\RequestValidationBundle\RequestValidator\RequestValidatorInterface;

class RequestValidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ServiceLocator $requestValidators,
        private ValidatorInterface $validator
    ) {}

    public function validateRequest(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_object($controller)) {
            $controllerClass = new \ReflectionClass($controller);
            $attributes = $controllerClass->getAttributes(RequestValidator::class);
        } else if (is_array($controller)) {
            $controllerClass = new \ReflectionClass($controller[0]);
            $attributes = $controllerClass->getMethod($controller[1])->getAttributes(RequestValidator::class);
        } else {
            return;
        }

        if (empty($attributes)) {
            return;
        }

        $request = $event->getRequest();
        /** @var RequestValidator $requestValidatorAttribute */
        $requestValidatorAttribute = $attributes[0]->newInstance();
        if (!$this->requestValidators->has($requestValidatorAttribute->validatorClass)) {
            throw new \RuntimeException(
                '"%s" not found or is not a valid request validator class.',
                $requestValidatorAttribute->validatorClass
            );
        }

        /** @var RequestValidatorInterface $requestValidator */
        $requestValidator = $this->requestValidators->get($requestValidatorAttribute->validatorClass);

        $data = $requestValidator instanceof DataReceiverInterface
            ? $requestValidator->getData($request)
            : $request->getContent();

        $errors = $this->validator->validate(
            $data,
            $requestValidator->getConstraint($request),
            $requestValidator->getGroups($request)
        );

        if ($errors->count() > 0) {
            $event->setController(function () use ($requestValidator, $errors, $request) {
                return $requestValidator->getInvalidRequestResponse($request, $errors);
            });
        }

        if ($requestValidator instanceof DataTransformerInterface) {
            $request->attributes->set(
                $requestValidator->getRequestAttributeName(),
                $requestValidator->transformData($data)
            );
        } else {
            $request->attributes->set('data', $data);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'validateRequest'
        ];
    }
}
