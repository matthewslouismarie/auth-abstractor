<?php

namespace LM\Authentifier\Model;

interface IMember
{
    /**
     * @todo Should it be id instead?
     */
    public function getUsername(): string;

    public function getHashedPassword(): string;
}
