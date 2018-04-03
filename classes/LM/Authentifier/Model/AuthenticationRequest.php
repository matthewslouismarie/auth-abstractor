<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Authentifier\IAuthentifier;
use LM\Authentifier\Authentifier\U2fAuthentifier;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Enum\AuthenticationRequest\Status;

/**
 * @todo Interface?
 */
class AuthenticationRequest
{
    private $config;

    private $status;

    public function __construct(IConfiguration $config, Status $status)
    {
        $this->config = $config;
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
        return new U2fAuthentifier($this->config);
    }
}
