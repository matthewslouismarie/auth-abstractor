<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Model\U2fRegistrationRequest;
use LM\AuthAbstractor\Factory\U2fRegistrationFactory;
use LM\AuthAbstractor\Test\KernelMocker;
use LM\AuthAbstractor\U2f\U2fServerGenerator;
use Firehed\U2F\SignRequest;
use Firehed\U2F\Registration;
use LM\Common\Model\ArrayObject;
use LM\AuthAbstractor\Mocker\U2fMocker;
use LM\AuthAbstractor\Model\IU2fRegistration;

class U2fRegistrationRequestTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = (new KernelMocker())
            ->createKernel()
            ->getContainer()
        ;
    }

    public function testSerialization()
    {
        $registerRequest = $this
            ->container
            ->get(U2fServerGenerator::class)
            ->getServer()
            ->generateRegisterRequest()
        ;
        $registrationRequest = new U2fRegistrationRequest($registerRequest);
        $this->assertEquals(
            $registrationRequest,
            unserialize(serialize($registrationRequest))
        );
    }

    public function testWithUnspecifiedSignRequests()
    {
        $registerRequest = $this
            ->container
            ->get(U2fServerGenerator::class)
            ->getServer()
            ->generateRegisterRequest()
        ;
        $registrationRequest = new U2fRegistrationRequest(
            $registerRequest
        );
        $this->assertSame($registerRequest, $registrationRequest->getRequest());
        $this->assertSame(
            json_encode($registerRequest),
            $registrationRequest->getRequestAsJson()
        );
        $this->assertSame(
            0,
            $registrationRequest->getSignRequests()->getSize()
        );
        $this->assertSame(
            '[]',
            $registrationRequest->getSignRequestsAsJson()
        );
    }

    public function testWithZeroSignRequests()
    {
        $registerRequest = $this
            ->container
            ->get(U2fServerGenerator::class)
            ->getServer()
            ->generateRegisterRequest()
        ;
        $registrationRequest = new U2fRegistrationRequest(
            $registerRequest,
            new ArrayObject([], SignRequest::class)
        );
        $this->assertSame($registerRequest, $registrationRequest->getRequest());
        $this->assertSame(
            json_encode($registerRequest),
            $registrationRequest->getRequestAsJson()
        );
        $this->assertSame(
            0,
            $registrationRequest->getSignRequests()->getSize()
        );
        $this->assertSame(
            '[]',
            $registrationRequest->getSignRequestsAsJson()
        );
    }

    public function testWithSignRequests()
    {
        $server = $this
            ->container
            ->get(U2fServerGenerator::class)
            ->getServer()
        ;
        $u2fFactory = $this
            ->container
            ->get(U2fRegistrationFactory::class)
        ;
        $registerRequest = $server->generateRegisterRequest();
        $firehedRegs = array_map(
            function (IU2fRegistration $reg) use ($u2fFactory): Registration {
                return $u2fFactory->toFirehed($reg);
            },
            $this
                ->container
                ->get(U2fMocker::class)
                ->getU2fRegistrationsOnly()
        );
        $signRequests = $server->generateSignRequests($firehedRegs);
        $registrationRequest = new U2fRegistrationRequest(
            $registerRequest,
            new ArrayObject($signRequests, SignRequest::class)
        );
        $this->assertSame($registerRequest, $registrationRequest->getRequest());
        $this->assertSame(
            json_encode($registerRequest),
            $registrationRequest->getRequestAsJson()
        );
        $this->assertSame(
            count($signRequests),
            $registrationRequest->getSignRequests()->getSize()
        );
        $this->assertSame(
            json_encode($signRequests),
            $registrationRequest->getSignRequestsAsJson()
        );
    }
}
