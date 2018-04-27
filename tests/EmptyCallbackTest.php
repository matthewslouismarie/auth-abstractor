<?php

declare(strict_types=1);

namespace Tests\LM;

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\EmptyCallback;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\Common\DataStructure\TypedMap;

class EmptyCallbackTest extends TestCase
{
    public function test()
    {
        $emptyCallback = new EmptyCallback();
        $process = new AuthenticationProcess(new TypedMap());
        $this->assertSame(
            '',
            $emptyCallback
                ->handleFailedProcess($process)
                ->getBody()
                ->__toString()
        );
        $this->assertEquals(
            '',
            $emptyCallback
                ->handleSuccessfulProcess($process)
                ->getBody()
                ->__toString()
        );
    }
}
