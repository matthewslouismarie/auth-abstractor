<?php

declare(strict_types=1);

namespace Tests\LM\ChallengeTest;

use Firehed\U2F\SignRequest;
use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Challenge\U2fChallenge;
use Symfony\Component\HttpFoundation\Request;
use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\Common\Enum\Scalar;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;
use LM\AuthAbstractor\Model\PersistOperation;

class U2fChallengeTest extends TestCase
{
    public function testValidPasswordSubmission()
    {
        $kernel = (new KernelMocker())->createKernel();

        $challenge = $kernel
            ->getContainer()
            ->get(U2fChallenge::class)
        ;

        $challengeResponse0 = $challenge->process(
            new AuthenticationProcess(
                new TypedMap([
                    'persist_operations' => new ArrayObject([], PersistOperation::class),
                    'username' => new StringObject(KernelMocker::USER_ID),
                    'used_u2f_key_public_keys' => new ArrayObject([], Scalar::_STR),
                ])
            ),
            null
        );
        $this->assertFalse($challengeResponse0->isFailedAttempt());
        $this->assertFalse($challengeResponse0->isFinished());
        $this->assertNotNull($challengeResponse0->getHttpResponse());
        $u2fData = $kernel
            ->getContainer()
            ->get(U2fMocker::class)
            ->get(3)['u2fAuthentications'][0]
        ;
        $authProcess1 = new AuthenticationProcess(
            $challengeResponse0
            ->getAuthenticationProcess()
            ->getTypedMap()
            ->set(
                'u2f_sign_requests',
                new ArrayObject($u2fData['signRequests'], SignRequest::class),
                ArrayObject::class
            )
        );
        $httpRequest1 = (new DiactorosFactory())->createRequest(Request::create(
            'http://localhost',
            'POST',
            [
                'form' => [
                    'u2fTokenResponse' => json_encode($u2fData['signResponse']),
                    '_token' => $kernel
                        ->getContainer()
                        ->get(IApplicationConfiguration::class)
                        ->getTokenStorage()
                        ->getToken('form'),
                ],
            ]
        ));
        $challengeResponse1 = $challenge->process(
            $authProcess1,
            $httpRequest1
        );
        $this->assertFalse($challengeResponse1->isFailedAttempt());
        $this->assertTrue($challengeResponse1->isFinished());
        $this->assertNull($challengeResponse1->getHttpResponse());
    }
}
