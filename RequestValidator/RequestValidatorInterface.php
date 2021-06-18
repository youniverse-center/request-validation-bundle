<?php

namespace Yc\RequestValidationBundle\RequestValidator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface RequestValidatorInterface
{
    public function getConstraint(Request $request): array|Constraint;
    public function getGroups(Request $request): array;
    public function getInvalidRequestResponse(Request $request, ConstraintViolationListInterface $errors): Response;
}
