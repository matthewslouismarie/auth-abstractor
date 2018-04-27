<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Form\Constraint;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Validator\PasswordValidator;
use Symfony\Component\Validator\Constraint;

/**
 * A constraint checking the given password meets the criteria specified in the
 * application configuration.
 *
 * @see \LM\AuthAbstractor\Challenge\PasswordUpdateChallenge
 * @see \LM\AuthAbstractor\Configuration\IApplicationConfiguration
 */
class ValidNewPassword extends Constraint
{
    /** @var IApplicationConfiguration */
    private $appConfig;

    /** @var PasswordValidator */
    private $pwdValidator;

    /**
     * @internal
     */
    public function __construct(
        IApplicationConfiguration $appConfig,
        PasswordValidator $pwdValidator
    ) {
        $this->appConfig = $appConfig;
        $this->pwdValidator = $pwdValidator;
    }

    /**
     * @return IApplicationConfiguration The configuration of the application.
     */
    public function getConfig(): IApplicationConfiguration
    {
        return $this->appConfig;
    }

    /**
     * @return PasswordValidator The PasswordValidator instance used for
     * validating the password in ValidNewPasswordValidator.
     *
     * @see ValidNewPasswordValidator
     */
    public function getPwdValidator(): PasswordValidator
    {
        return $this->pwdValidator;
    }
}
