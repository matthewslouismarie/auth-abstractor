<?php

declare(strict_types=1);

namespace LM\Authentifier\Form\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidNewPasswordValidator extends ConstraintValidator
{
    public function validate($password, Constraint $constraint)
    {
        $pwdValidator = $constraint->getPwdValidator();
        $pwdConfig = $constraint->getConfig()->getPwdSettings();
        if (true === $pwdConfig['enforce_min_length']) {
            $pwdMinLength = $pwdConfig['min_length'];
            if (mb_strlen($password, 'utf-8') < $pwdMinLength) {
                $this->addError("Your password needs to be at least {$pwdMinLength} characters long", $password);
            }
        }
        if (true === $pwdConfig['numbers']) {
            switch (preg_match('/[0-9]/', $password)) {
                case 0:
                    $this->addError('Your password needs to contain numbers.', $password);
                    break;

                case false:
                    throw new Exception();
                    break;
            }
        }
        if (true === $pwdConfig['special_chars']) {
            if (false === $pwdValidator->hasSpecialChars($password)) {
                $this->addError('Your password needs to contain special characters', $password);
            }
        }
        if (true === $pwdConfig['uppercase']) {
            switch (preg_match('/[A-Z]/', $password)) {
                case 0:
                    $this->addError('Your password needs to contain uppercase letters.', $password);
                    break;

                case false:
                    throw new Exception();
                    break;
            }
        }
    }

    private function addError(string $message, string $password): void
    {
        $this
            ->context
            ->buildViolation($message)
            ->setParameter('{{ string }}', $password)
            ->addViolation()
        ;
    }
}
