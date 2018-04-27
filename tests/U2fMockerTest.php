<?php

declare(strict_types=1);

namespace Tests\LM;

use Firehed\U2f\RegisterRequest;
use LM\AuthAbstractor\Test\KernelMocker;
use Firehed\U2F\SignRequest;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\AuthAbstractor\Mocker\U2fMocker;

class U2fMockerTest extends TestCase
{
    /**
     * @todo Use PHPUnit type tests.
     */
    public function testU2fMocker()
    {
        $kernel = (new KernelMocker())->createKernel();
        $u2fMocker = $kernel
            ->getContainer()
            ->get(U2fMocker::class)
        ;

        $nU2fRegistrations = count(
            json_decode(file_get_contents(__DIR__.'/../u2f_registrations.json'))
        );

        $this->assertSame($nU2fRegistrations, count($u2fMocker->getU2fRegistrations()));

        $u2fRegData = $u2fMocker->getU2fRegistrations();
        foreach ($u2fRegData as $id => $u2fRegDatum) {
            $this->assertTrue(is_int($id));
            $this->assertTrue(isset($u2fRegDatum['u2fRegistration']));
            $this->assertTrue(is_a($u2fRegDatum['u2fRegistration'], IU2fRegistration::class));
            if (isset($u2fRegDatum['registerResponseStr'])) {
                $this->assertTrue($this->isValidJson($u2fRegDatum['registerResponseStr']));
            }
            if (isset($u2fRegDatum['registerRequest'])) {
                $this->assertTrue(is_a($u2fRegDatum['registerRequest'], RegisterRequest::class));
            }
        }

        $u2fReg3 = $u2fMocker->get(3);
        $this->assertTrue(isset($u2fReg3['u2fAuthentications']));
        $u2fAuth3 = $u2fReg3['u2fAuthentications'];
        $this->assertTrue(is_array($u2fAuth3));
        foreach ($u2fAuth3 as $u2fAuth) {
            $this->assertTrue(isset($u2fAuth['signRequests']));
            $this->assertTrue(is_array($u2fAuth['signRequests']));
            $this->assertTrue(is_a($u2fAuth['signRequests'][0], SignRequest::class));
            $this->assertTrue(isset($u2fAuth['signResponse']));
            $this->assertTrue(is_array($u2fAuth['signResponse']));
        }

        $this->assertSame(4, count($u2fMocker->getU2fRegistrationsOnly()));
    }

    /**
     * @link https://stackoverflow.com/a/6041773/7089212
     * @todo Move in a service?
     */
    public function isValidJson($string): bool
    {
        json_decode($string);

        return (JSON_ERROR_NONE === json_last_error());
    }
}
