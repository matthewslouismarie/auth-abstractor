<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Firehed\U2F\RegisterRequest;
use Firehed\U2F\SignRequest;
use LM\Common\Model\ArrayObject;
use Serializable;

/**
 * This class is only used to store a generated U2F register request so that
 * it can returned by some functions.
 *
 * @see \LM\AuthAbstractor\U2f\U2fRegistrationManager
 */
class U2fRegistrationRequest implements Serializable
{
    private $request;

    private $signRequests;

    public function __construct(RegisterRequest $request, ?ArrayObject $signRequests = null)
    {
        $this->request = $request;
        if (null !== $signRequests) {
            $this->signRequests = $signRequests->toArray(SignRequest::class);
        }
    }

    public function getRequest(): RegisterRequest
    {
        return $this->request;
    }

    public function getRequestAsJson(): string
    {
        return json_encode($this->request);
    }

    public function getSignRequests(): ArrayObject
    {
        return new ArrayObject($this->signRequests, SignRequest::class);
    }

    public function getSignRequestsAsJson(): ?string
    {
        return json_encode($this->signRequests);
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
