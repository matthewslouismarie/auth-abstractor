<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Enum\AuthenticationRequest\Status;

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
}
