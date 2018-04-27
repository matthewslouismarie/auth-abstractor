<?php

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Validator\PasswordValidator;

class PasswordValidatorTest extends TestCase
{
    public function testPasswordValidator()
    {
        $pwdValidator = new PasswordValidator();
        $this->assertTrue($pwdValidator->hasSpecialChars('/'));
        $this->assertFalse($pwdValidator->hasSpecialChars('euie'));
    }
}
