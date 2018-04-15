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
use LM\Common\Enum\Scalar;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessFactory
{
    public function createProcess(
        array $challenges,
        IAuthenticationCallback $callback = null,
        ?string $username = null): AuthenticationProcess
    {
        $dataArray = [
            new RequestDatum("used_u2f_key_public_keys", new ArrayObject([], Scalar::_STR)),
            new RequestDatum("challenges", new ArrayObject($challenges, Scalar::_STR)),
            new RequestDatum("max_n_failed_attempts", new IntegerObject(3)),
            new RequestDatum("n_failed_attempts", new IntegerObject(0)),
            new RequestDatum("callback", $callback),
            new RequestDatum("status", new Status(Status::ONGOING)),
            new RequestDatum('u2f_registrations', new ArrayObject([], Registration::class)),
        ];
        if (null !== $username) {
            $dataArray[] = new RequestDatum('username', new StringObject($username));
        }
        $dataManager = new DataManager($dataArray);

        return new AuthenticationProcess($dataManager);
    }
}
