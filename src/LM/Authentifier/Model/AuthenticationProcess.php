<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use Serializable;

/**
 * @todo Interface
 * @todo Maybe the data manager should never be manipulated directly, but only
 * indirectly through AuthenticationProcess? Would make things simpler.
 */
class AuthenticationProcess implements Serializable
{
    private $dataManager;

    /**
     * @todo Check authentifier names are valid here.
     * @todo Should only take a dataManager.
     * @todo Should validate the data manager.
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
            ->getCurrentItem(Scalar::_STR)
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

    public function getMember(): IMember
    {
        return $this
            ->dataManager
            ->get(RequestDatum::KEY_PROPERTY, 'member')
            ->getOnlyValue()
            ->get(RequestDatum::VALUE_PROPERTY, IMember::class)
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

    public function getU2fRegistrations(): array
    {
        return $this
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, 'u2f_registrations')
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, ArrayObject::class)
            ->toArray(IU2fRegistration::class)
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
