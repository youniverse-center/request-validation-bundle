<?php

namespace Yc\RequestValidationBundle\DataTransformer;

interface DataTransformerInterface
{
    public function transformData(mixed $data): array;
}
