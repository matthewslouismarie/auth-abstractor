<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IMember;
use Serializable;

/**
 * This is a convenience implementation of IMember. It only serves as data
 * object storing two strings: a username and a hashed password.
 */
class Member implements IMember, Serializable
{
    /** @var string */
    private $hashedPassword;

    /** @var string */
    private $username;

    /**
     * @api
     * @param string $hashedPassword The hashed password of the member.
     * @param string $username The member's username.
     */
    public function __construct(string $hashedPassword, string $username)
    {
        $this->hashedPassword = $hashedPassword;
        $this->username = $username;
    }

    /**
     * @return string The hashed password of the member.
     */
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @return string The username of the member.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function serialize()
    {
        return serialize([
            $this->hashedPassword,
            $this->username,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->hashedPassword,
            $this->username) = unserialize($serialized);
    }
}
