<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\U2fRegistration;

class U2fRegistrationTest extends TestCase
{
    public function test()
    {
        $registration = new U2fRegistration(
            'certificate',
            0,
            'keyhandle',
            'publickey'
        );
        $this->assertSame('certificate', $registration->getAttestationCertificate());
        $this->assertSame(0, $registration->getCounter());
        $this->assertSame('keyhandle', $registration->getKeyHandle());
        $this->assertSame('publickey', $registration->getPublicKey());
        $this->assertEquals(
            $registration,
            unserialize(serialize($registration))
        );
    }
}
