<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IMember;
use Serializable;

class Member implements IMember, Serializable
{
    private $hashedPassword;

    private $username;

    public function __construct(string $hashedPassword, string $username)
    {
        $this->hashedPassword = $hashedPassword;
        $this->username = $username;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

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
