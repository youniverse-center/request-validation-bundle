<?php

namespace Yc\RequestValidationBundle\Attributes;

#[\Attribute]
class RequestValidator
{
    public function __construct(
        public string $validatorClass
    ) {}
}
