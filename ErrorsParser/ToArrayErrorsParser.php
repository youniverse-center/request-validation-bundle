<?php

namespace Yc\RequestValidationBundle\ErrorsParser;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ToArrayErrorsParser
{
    public function __invoke(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];

        foreach ($errors as $error) {
            $path = $error->getPropertyPath();
            if (empty($path)) {
                $errorMessages['__GLOBAL__'][] = $error->getMessage();
            } else {
                $path = trim($path, '[]');
                $path = str_replace('][', '.', $path);
                $errorMessages[$path][] = $error->getMessage();
            }
        }

        return $errorMessages;
    }
}
