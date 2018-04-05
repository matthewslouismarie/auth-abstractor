<?php

namespace LM\Authentifier\Validator;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExistingMemberValidator extends ConstraintValidator
{
    private $config;

    public function __construct(IApplicationConfiguration $config)
    {
        $this->config = $config;
    }

    public function validate($username, Constraint $constraint)
    {
        if (!is_string($username)
        || !$this->config->isExistingMember($username)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $username)
                ->addViolation();
        }
    }
}
