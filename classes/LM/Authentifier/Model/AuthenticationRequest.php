<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Enum\AuthenticationRequest\Status;
use LM\Authentifier\Authentifier\IAuthentifier;
use LM\Authentifier\Authentifier\U2fAuthentifier;

/**
 * @todo Interface?
 */
class AuthenticationRequest
{
    private $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @todo
     */
    public function getCurrentAuthentifier(): IAuthentifier
    {
        return new U2fAuthentifier();
    }
}
