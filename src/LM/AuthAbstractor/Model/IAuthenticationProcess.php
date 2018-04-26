<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use LM\AuthAbstractor\Enum\AuthenticationProcess\Status;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\ArrayObject;
use Serializable;

/**
 * Interface for authentication processes. Any implementation must be immutable.
 *
 * @todo Use interfaces or scalars for ArrayObject, and Status.
 * @todo It is heavily coupled with specific challenges (e.g. getMember()).
 */
interface IAuthenticationProcess extends Serializable
{
    /**
     * @api
     * @deprecated
     * @return ArrayObject An ArrayObject of challenges.
     */
    public function getChallenges(): ArrayObject;

    /**
     * @return string The FQCN of the current challenge.
     */
    public function getCurrentChallenge(): string;

    /**
     * @api
     * @deprecated
     * @internal
     * @return TypeMap
     * @todo Delete. This makes the rest of auth-abstractor rely on a specific
     * implementation.
     */
    public function getTypedMap(): TypedMap;

    /**
     * @api
     * @return int The maximum number of failed attempts in a row allowed before
     * the authentication process fails.
     * @todo The maximum number of failed attempts should be per challenge and
     * not per authentication process.
     */
    public function getMaxNFailedAttempts(): int;

    /**
     * @api
     * @return IMember The currently logged-in member.
     * @todo Should be able to return null if the user is not logged-in!
     */
    public function getMember(): IMember;

    /**
     * @api
     * @return int The current number of failed attempts the user made. It must
     * go back to zero whenever the user succeeds a submission.
     */
    public function getNFailedAttempts(): int;

    /**
     * @api
     * @return PersistOperation[] An array of operations that the application
     * must persist somehow (e.g. in a database).
     */
    public function getPersistOperations(): array;

    /**
     * @api
     * @return Status The status of the authentication process (ongoing, failed,
     * or succeeded).
     */
    public function getStatus(): Status;

    /**
     * @api
     * @deprecated
     * @return string The username of the currently logged-in user.
     * @todo Delete, since getMember() already exists.
     */
    public function getUsername(): string;

    /**
     * @api
     * This method must not modify the current authentication process.
     *
     * @return IAuthenticationProcess This must return a copy of itself with
     * the number of failed attempts incremented by one.
     */
    public function incrementNFailedAttempts(): IAuthenticationProcess;

    /**
     * @api
     * @return bool Whether the authentication process failed.
     */
    public function isFailed(): bool;

    /**
     * @api
     * @return bool Whether the authentication process finished.
     */
    public function isFinished(): bool;

    /**
     * @api
     * @return bool Whether the authentication process is ongoing.
     */
    public function isOngoing(): bool;

    /**
     * @api
     * @return bool Whether the authentication process succeeded.
     */
    public function isSucceeded(): bool;

    /**
     * @api
     * @return IAuthenticationProcess A copy of itself with the number of failed
     * attempts reset to 0.
     */
    public function resetNFailedAttempts(): IAuthenticationProcess;

    /**
     * @api
     * @param int $nFailedAttempts The number of failed attempts.
     * @return IAuthenticationProcess A copy of itself with the updated number
     * of failed attempts.
     */
    public function setNFailedAttempts(int $nFailedAttempts): IAuthenticationProcess;

    /**
     * @api
     * @return IAuthenticationProcess A copy of itself set to use the next
     * challenge.
     */
    public function setToNextChallenge(): IAuthenticationProcess;
}
