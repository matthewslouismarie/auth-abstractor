<?php

namespace LM\Authentifier\Factory;

use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationRequestFactory
{
    public function createU2fAuthenticationRequest(
        string $username,
        IConfiguration $userConfiguration): AuthenticationRequest
    {
        $dataManager = new DataManager([
            new RequestDatum("username", new StringObject($username)),
            new RequestDatum("used_u2f_key_ids", new ArrayObject([])),
        ]);

        return new AuthenticationRequest(
            $dataManager,
            $userConfiguration,
            new Status(Status::ONGOING)
        );
    }
}