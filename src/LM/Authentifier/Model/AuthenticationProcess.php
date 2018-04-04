<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Authentifier\U2fAuthentifier;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\PersistOperation;
use Serializable;

/**
 * @todo Interface?
 */
class AuthenticationProcess implements Serializable
{
    private $callback;

    private $config;

    private $dataManager;

    private $status;

    public function __construct(
        IApplicationConfiguration $config,
        DataManager $dataManager,
        Status $status,
        IAuthenticationCallback $callback)
    {
        $this->callback = $callback;
        $this->config = $config;
        $this->dataManager = $dataManager;
        $this->status = $status;
    }

    public function getCallback(): IAuthenticationCallback
    {
        return $this->callback;
    }

    public function getConfiguration(): IApplicationConfiguration
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
            $this->callback,
            $this->config,
            $this->dataManager,
            $this->status,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->callback,
            $this->config,
            $this->dataManager,
            $this->status) = unserialize($serialized);
    }
}
