<?php

declare(strict_types=1);

namespace LM\Authentifier\Model;

use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;
use Serializable;

/**
 * @todo Interface
 */
class AuthenticationProcess implements Serializable
{
    private $typedMap;

    public function __construct(TypedMap $typedMap)
    {
        $this->typedMap = $typedMap;
    }

    public function getChallenges(): ArrayObject
    {
        return $this
            ->typedMap
            ->get('challenges', ArrayObject::class)
        ;
    }

    public function getCallback(): IAuthenticationCallback
    {
        return $this
            ->typedMap
            ->get('callback', IAuthenticationCallback::class)
        ;
    }

    /**
     * @todo Should check for validity.
     */
    public function getCurrentChallenge(): string
    {
        return $this
            ->typedMap
            ->get('challenges', ArrayObject::class)
            ->getCurrentItem(Scalar::_STR)
        ;
    }

    public function getTypedMap(): TypedMap
    {
        return $this->typedMap;
    }

    public function getMaxNFailedAttempts(): int
    {
        return $this
            ->typedMap
            ->get('max_n_failed_attempts', IntegerObject::class)
            ->toInteger()
        ;
    }

    public function getMember(): IMember
    {
        return $this
            ->typedMap
            ->get('member', IMember::class)
        ;
    }

    public function getNFailedAttempts(): int
    {
        return $this
            ->typedMap
            ->get('n_failed_attempts', IntegerObject::class)
            ->toInteger()
        ;
    }

    public function getPersistOperations(): array
    {
        return $this
            ->typedMap
            ->get('persist_operations', ArrayObject::class)
            ->toArray(PersistOperation::class)
        ;
    }

    public function getStatus(): Status
    {
        return $this
            ->typedMap
            ->get('status', Status::class)
        ;
    }

    public function getU2fRegistrations(): array
    {
        return $this
            ->typedMap
            ->get('u2f_registrations', ArrayObject::class)
            ->toArray(IU2fRegistration::class)
        ;
    }

    public function getUsername(): string
    {
        return $this
            ->typedMap
            ->get('username', StringObject::class)
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
            ->typedMap
            ->set(
                'n_failed_attempts',
                new IntegerObject($nFailedAttempts),
                IntegerObject::class
            )
        ;
        if ($nFailedAttempts < $this->getMaxNFailedAttempts()) {
            return new self($newDm);
        } else {
            return new self($newDm
                ->set(
                    'status',
                    new Status(Status::FAILED),
                    Status::class
                ))
            ;
        }
    }

    public function setToNextChallenge(): self
    {
        $challenges = $this->getChallenges();
        if ($challenges->hasNextItem()) {
            $challenges->setToNextItem();
            return new self($this
                ->typedMap
                ->set(
                    'challenges',
                    $challenges,
                    ArrayObject::class
                ))
            ;
        } else {
            return new self($this
                ->typedMap
                ->set(
                    'status',
                    new Status(Status::SUCCEEDED),
                    Status::class
                ))
            ;
        }
    }

    public function serialize()
    {
        return serialize($this->typedMap);
    }

    public function unserialize($serialized)
    {
        $this->typedMap = unserialize($serialized);
    }
}
