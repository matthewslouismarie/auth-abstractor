<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Challenge\U2fChallenge;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\PersistOperation;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;
use Serializable;

/**
 * @todo Interface?
 * @todo Data shouldn't be stored here.
 */
class AuthenticationProcess implements Serializable
{
    private $dataManager;

    /**
     * @todo Check authentifier names are valid here.
     * @todo Should only take a dataManager.
     */
    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function getChallenges(): ArrayObject
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "challenges")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
        ;
    }

    public function getCallback(): IAuthenticationCallback
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "callback")
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, IAuthenticationCallback::class)
        ;
    }

    /**
     * @todo Should check for validity.
     */
    public function getCurrentChallenge(): string
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "challenges")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ->getCurrentItem(StringObject::class)
            ->toString()
        ;
    }

    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    public function getPersistOperations(): array
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "persist_operations")
            ->toArrayOfObjects(RequestDatum::VALUE_PROPERTY, PersistOperation::class)
        ;
    }

    public function getStatus(): Status
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "status")
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, Status::class)
        ;
    }

    public function getUsername(): string
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "username")
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, PersistOperation::class)
            ->toString()
        ;
    }

    public function serialize()
    {
        return serialize($this->dataManager);
    }

    public function unserialize($serialized)
    {
        $this->dataManager = unserialize($serialized);
    }
}
