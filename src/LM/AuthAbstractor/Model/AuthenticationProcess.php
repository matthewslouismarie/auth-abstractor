<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\StringObject;

/**
 * This class is an implementation of IAuthenticationProcess. It stores the data
 * associated with an authentication process.
 *
 * @todo Merge IAuthenticationProcess and TypedMap
 */
class AuthenticationProcess implements IAuthenticationProcess
{
    /** @var TypedMap */
    private $typedMap;

    /**
     * @param TypedMap $typedMap
     */
    public function __construct(?TypedMap $typedMap = null)
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
        if ($this->typedMap->has('persist_operations')) {
            return $this
                ->typedMap
                ->get('persist_operations', ArrayObject::class)
                ->toArray(PersistOperation::class)
            ;
        } else {
            return [];
        }
    }

    public function getStatus(): Status
    {
        return $this
            ->typedMap
            ->get('status', Status::class)
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

    public function incrementNFailedAttempts(): IAuthenticationProcess
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

    public function resetNFailedAttempts(): IAuthenticationProcess
    {
        return $this->setNFailedAttempts(0);
    }

    /**
     * @internal
     */
    public function setNFailedAttempts(int $nFailedAttempts): IAuthenticationProcess
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

    /**
     * @internal
     */
    public function setToNextChallenge(): IAuthenticationProcess
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
