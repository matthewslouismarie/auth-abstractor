<?php

namespace LM\Authentifier\Form\Constraint;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Validator\PasswordValidator;
use Symfony\Component\Validator\Constraint;

class ValidNewPassword extends Constraint
{
    private $config;

    private $pwdValidator;

    public function __construct(
        IApplicationConfiguration $config,
        PasswordValidator $pwdValidator
    ) {
        $this->config = $config;
        $this->pwdValidator = $pwdValidator;
    }

    public function getConfig(): IApplicationConfiguration
    {
        return $this->config;
    }

    public function getPwdValidator(): PasswordValidator
    {
        return $this->pwdValidator;
    }
}
