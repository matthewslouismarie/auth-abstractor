<?php

namespace LM\Authentifier\Factory;

use Firehed\U2F\Registration;
use LM\Authentifier\Challenge\ExistingUsernameChallenge;
use LM\Authentifier\Challenge\U2fChallenge;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessFactory
{
    public function createAnonymousU2fProcess(
        array $authentifiers,
        IAuthenticationCallback $callback = null): AuthenticationProcess
    {
        $dataManager = new DataManager([
            new RequestDatum("used_u2f_key_ids", new ArrayObject([], IntegerObject::class)),
            new RequestDatum("challenges", new ArrayObject($authentifiers, "string")),
            new RequestDatum("max_n_failed_attempts", new IntegerObject(3)),
            new RequestDatum("n_failed_attempts", new IntegerObject(0)),
            new RequestDatum("callback", $callback),
            new RequestDatum("status", new Status(Status::ONGOING)),
        ]);

        return new AuthenticationProcess($dataManager);
    }
}
