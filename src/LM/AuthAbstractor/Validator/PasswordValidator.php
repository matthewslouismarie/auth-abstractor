<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Validator;

/**
 * This class contains methods to validate passwords.
 */
class PasswordValidator
{
    /**
     * Validates that a password contains special characters.
     *
     * @param string $password The password we want to validate.
     * @return bool Whether the password is valid or not.
     */
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
