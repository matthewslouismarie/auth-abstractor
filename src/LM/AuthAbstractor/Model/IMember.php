<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

/**
 * Interface for reprensenting a member.
 */
interface IMember
{
    /**
     * @todo Should it be id instead?
     */
    public function getUsername(): string;

    public function getHashedPassword(): string;
}
