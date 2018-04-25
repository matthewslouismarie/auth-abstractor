<?php

declare(strict_types=1);

namespace LM\Authentifier\Factory;

use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Authentifier\Model\IU2fRegistration;
use LM\Authentifier\Model\PersistOperation;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessFactory
{
    /**
     * @todo Put $additionalData in a separate scope.
     */
    public function createProcess(
        array $challenges,
        int $maxNFailedAttempts = 3,
        ?string $username = null,
        array $additionalData = []
    ): AuthenticationProcess {
        $dataArray = array_merge($additionalData, [
            'used_u2f_key_public_keys' => new ArrayObject([], Scalar::_STR),
            'challenges' => new ArrayObject($challenges, Scalar::_STR),
            'max_n_failed_attempts' => new IntegerObject($maxNFailedAttempts),
            'n_failed_attempts' => new IntegerObject(0),
            'persist_operations' => new ArrayObject([], PersistOperation::class),
            'status' => new Status(Status::ONGOING),
            'u2f_registrations' => new ArrayObject([], IU2fRegistration::class),
            'new_u2f_registrations' => new ArrayObject([], IU2fRegistration::class),
            'n_u2f_registrations' => new IntegerObject(0),
        ]);
        if (null !== $username) {
            $dataArray['username'] = new StringObject($username);
        }
        $typedMap = new TypedMap($dataArray);

        return new AuthenticationProcess($typedMap);
    }
}
