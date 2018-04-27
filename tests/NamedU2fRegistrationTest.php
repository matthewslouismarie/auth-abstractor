<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\NamedU2fRegistration;

class NamedU2fRegistrationTest extends TestCase
{
    public function test()
    {
        $u2fRegistration = new NamedU2fRegistration(
            'certificate',
            0,
            'keyhandle',
            'myname',
            'publickey'
        );
        $this->assertSame(
            'certificate',
            $u2fRegistration->getAttestationCertificate()
        );
        $this->assertSame(0, $u2fRegistration->getCounter());
        $this->assertSame('keyhandle', $u2fRegistration->getKeyHandle());
        $this->assertSame('publickey', $u2fRegistration->getPublicKey());
        $this->assertSame('myname', $u2fRegistration->getName());
        $this->assertEquals(
            $u2fRegistration,
            unserialize(serialize($u2fRegistration))
        );
    }
}
