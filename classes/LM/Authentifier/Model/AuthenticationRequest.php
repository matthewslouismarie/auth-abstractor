<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Authentifier\U2fAuthentifier;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use LM\Authentifier\Model\DataManager;

/**
 * @todo Interface?
 */
class AuthenticationRequest
{
    private $config;

    private $dataManager;

    private $status;

    public function __construct(
        DataManager $dataManager,
        IConfiguration $config,
        Status $status)
    {
        $this->config = $config;
        $this->dataManager = $dataManager;
        $this->status = $status;
    }

    public function getConfiguration(): IConfiguration
    {
        return $this->config;
    }

    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @todo
     */
    public function getCurrentAuthentifier(): string
    {
        return U2fAuthentifier::class;
    }
}
