<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

/**
 * Interface for reprensenting a member.
 */
interface IMember
{
    /**
     * @api
     * @return string The member's username.
     * @todo Should it be id instead?
     */
    public function getUsername(): string;

    /**
     * @api
     * @return string The member's hashed pasword.
     */
    public function getHashedPassword(): string;
}
