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
 * @todo Delete?
 * @internal
 * @see \LM\AuthAbstractor\U2f\U2fRegistrationManager
 */
class U2fRegistrationRequest implements Serializable
{
    /** @var RegisterRequest */
    private $request;

    /** @var SignRequest[] */
    private $signRequests;

    /**
     * @param RegisterRequest $request A Firehed register request.
     * @param null|ArrayObject $signRequests An array of sign requests, to
     * prevent the user (or rather, maket it easier for them) from registering
     * the same U2F token twice.
     */
    public function __construct(
        RegisterRequest $request,
        ?ArrayObject $signRequests = null
    ) {
        $this->request = $request;
        if (null !== $signRequests) {
            $this->signRequests = $signRequests->toArray(SignRequest::class);
        } else {
            $this->signRequests = [];
        }
    }

    /**
     * @return RegisterRequest The Firehed register request.
     */
    public function getRequest(): RegisterRequest
    {
        return $this->request;
    }

    /**
     * @return string The U2F register request as a JSON string.
     */
    public function getRequestAsJson(): string
    {
        return json_encode($this->request);
    }

    /**
     * @return ArrayObject An array of sign requests.
     */
    public function getSignRequests(): ArrayObject
    {
        return new ArrayObject($this->signRequests, SignRequest::class);
    }

    /**
     * A JSON representing the sign requests.
     *
     * @todo Should it be nullable?
     */
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
