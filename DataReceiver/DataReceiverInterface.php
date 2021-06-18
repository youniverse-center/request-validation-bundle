<?php

namespace Yc\RequestValidationBundle\DataReceiver;

use Symfony\Component\HttpFoundation\Request;

interface DataReceiverInterface
{
    public function getData(Request $request): mixed;
}
