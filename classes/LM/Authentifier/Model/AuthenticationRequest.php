<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Authentifier\U2fAuthentifier;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\PersistOperation;
use Serializable;

/**
 * @todo Interface?
 */
class AuthenticationRequest implements Serializable
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

    public function getPersistOperations(): array
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "persist_operations")
            ->toArrayOfObjects(RequestDatum::VALUE_PROPERTY, PersistOperation::class)
        ;
    }

    /**
     * @todo
     */
    public function getCurrentAuthentifier(): string
    {
        return U2fAuthentifier::class;
    }

    public function serialize()
    {
        return serialize([
            $this->config,
            $this->dataManager,
            $this->status,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->config,
            $this->dataManager,
            $this->status) = unserialize($serialized);
    }
}
