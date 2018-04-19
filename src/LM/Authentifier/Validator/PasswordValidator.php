<?php

declare(strict_types=1);

namespace LM\Authentifier\Validator;

class PasswordValidator
{
    public function hasSpecialChars(string $password): bool
    {
        switch (preg_match('/[^a-zA-Z0-9]/', $password)) {
            case 0:
                return false;

            case 1:
                return true;

            case false:
                throw new Exception();
        }
    }
}
