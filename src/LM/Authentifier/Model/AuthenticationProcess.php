<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Challenge\U2fChallenge;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\PersistOperation;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
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
            ->getCurrentItem("string")
        ;
    }

    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    public function getMaxNFailedAttempts(): int
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "max_n_failed_attempts")
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, IntegerObject::class)
            ->toInteger()
        ;
    }

    public function getNFailedAttempts(): int
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, "n_failed_attempts")
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, IntegerObject::class)
            ->toInteger()
        ;
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

    public function incrementNFailedAttempts(): self
    {
        return $this->setNFailedAttempts($this->getNFailedAttempts() + 1);
    }

    public function isFailed(): bool
    {
        return $this->getStatus()->is(new Status(Status::FAILED));
    }

    public function isFinished(): bool
    {
        return $this->isFailed() || $this->isSucceeded();
    }

    public function isOngoing(): bool
    {
        return $this->getStatus()->is(new Status(Status::ONGOING));
    }

    public function isSucceeded(): bool
    {
        return $this->getStatus()->is(new Status(Status::SUCCEEDED));
    }

    public function resetNFailedAttempts(): self
    {
        return $this->setNFailedAttempts(0);
    }

    public function setNFailedAttempts(int $nFailedAttempts): self
    {
        $newDm = $this
            ->dataManager
            ->replace(
                new RequestDatum(
                    "n_failed_attempts",
                    new IntegerObject($nFailedAttempts)),
                RequestDatum::KEY_PROPERTY)
        ;
        if ($nFailedAttempts < $this->getMaxNFailedAttempts()) {
            return new self($newDm);
        } else {
            return new self($newDm
                ->replace(
                    new RequestDatum(
                        "status",
                        new Status(Status::FAILED)),
                    RequestDatum::KEY_PROPERTY))
            ;
        }
    }

    public function setToNextChallenge(): self
    {
        $challenges = $this->getChallenges();
        if ($challenges->hasNextItem()) {
            $challenges->setToNextItem();
            return new self($this
                ->dataManager
                ->replace(
                    new RequestDatum(
                        "challenges",
                        $challenges),
                    RequestDatum::KEY_PROPERTY))
            ;
        } else {
            return new self($this->dataManager
                ->replace(
                    new RequestDatum(
                        "status",
                        new Status(Status::SUCCEEDED)),
                    RequestDatum::KEY_PROPERTY))
            ;
        }

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
