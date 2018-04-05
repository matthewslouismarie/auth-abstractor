<?php

namespace LM\Authentifier\Form\Submission;

use LM\Authentifier\Validator\ExistingMember;
use Serializable;

class ExistingUsernameSubmission implements Serializable
{
    /**
     * @ExistingMember
     */
    private $username;

    public function __construct(?string $username = null)
    {
        $this->username = $username;
    }

    public function setUsername(?string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function serialize(): string
    {
        return serialize([
            $this->username,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->username) = unserialize($serialized);
    }
}
