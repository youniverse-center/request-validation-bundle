<?php

namespace Yc\RequestValidationBundle\DataReceiver;

use Symfony\Component\HttpFoundation\Request;

class JsonContentDataReceiver implements DataReceiverInterface
{
    public function getData(Request $request): mixed
    {
        return json_decode($request->getContent(), true);
    }
}
