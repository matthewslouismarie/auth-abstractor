<?php

namespace LM\Authentifier\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExistingMember extends Constraint
{
    public $message = 'Your username or your password is not valid';
}
