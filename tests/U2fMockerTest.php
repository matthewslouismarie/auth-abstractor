<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use LM\AuthAbstractor\Implementation\Member;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\AuthAbstractor\Test\KernelMocker;
use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Mocker\U2fMocker;

class U2fMockerTest extends TestCase
{
    public function testU2fMocker()
    {
        $kernel = (new KernelMocker())->getKernel();
        $u2fMocker = $kernel
            ->getContainer()
            ->get(U2fMocker::class)
        ;

        $nU2fRegistrations = count(
            json_decode(file_get_contents(__DIR__.'/../u2f_registrations.json'))
        );

        $this->assertSame($nU2fRegistrations, count($u2fMocker->getU2fRegistrations()));
    }
}
