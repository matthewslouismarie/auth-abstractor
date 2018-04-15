<?php

namespace LM\Authentifier\Model;

use Firehed\U2F\RegisterRequest;
use Serializable;

class U2fRegistrationRequest implements Serializable
{
    private $request;

    private $signRequests;

    public function __construct(RegisterRequest $request, string $signRequests)
    {
        $this->request = $request;
        $this->signRequests = $signRequests;
    }

    public function getRequest(): RegisterRequest
    {
        return $this->request;
    }

    public function getRequestAsJson(): string
    {
        return json_encode($this->request);
    }

    public function getSignRequests(): string
    {
        return $this->signRequests;
    }

    public function serialize()
    {
        return serialize([
            $this->request,
            $this->signRequests,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->request,
            $this->signRequests) = unserialize($serialized);
    }
}
