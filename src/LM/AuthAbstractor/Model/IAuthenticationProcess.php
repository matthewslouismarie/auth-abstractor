<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\ArrayObject;
use Serializable;

interface IAuthenticationProcess extends Serializable
{
    public function getChallenges(): ArrayObject;

    public function getCurrentChallenge(): string;

    public function getTypedMap(): TypedMap;

    public function getMaxNFailedAttempts(): int;

    public function getMember(): IMember;

    public function getNFailedAttempts(): int;

    public function getPersistOperations(): array;

    public function getStatus(): Status;

    public function getUsername(): string;

    public function incrementNFailedAttempts(): IAuthenticationProcess;

    public function isFailed(): bool;

    public function isFinished(): bool;

    public function isOngoing(): bool;

    public function isSucceeded(): bool;

    public function resetNFailedAttempts(): IAuthenticationProcess;

    public function setNFailedAttempts(int $nFailedAttempts): IAuthenticationProcess;

    public function setToNextChallenge(): IAuthenticationProcess;
}
