<?php

namespace LM\Authentifier\Factory;

use Firehed\U2F\Registration;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessFactory
{
    public function createU2fProcess(
        string $username,
        array $u2fRegistrationsArray,
        IApplicationConfiguration $appConfig,
        ?IAuthenticationCallback $callback = null): AuthenticationProcess
    {
        $dataManager = new DataManager([
            new RequestDatum("username", new StringObject($username)),
            new RequestDatum("used_u2f_key_ids", new ArrayObject([], IntegerObject::class)),
            new RequestDatum("u2f_registrations", new ArrayObject($u2fRegistrationsArray, Registration::class)),
        ]);

        return new AuthenticationProcess(
            $appConfig,
            $dataManager,
            new Status(Status::ONGOING),
            $callback
        );
    }
}
