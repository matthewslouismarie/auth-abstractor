<?php

declare(strict_types=1);

namespace Tests\LM;

use LM\AuthAbstractor\Implementation\TestMailer;
use PHPUnit\Framework\TestCase;

class TestMailerTest extends TestCase
{
    public function test()
    {
        $mailer = new TestMailer();
        $mailer->send('you@localhost', 'hello');
        $this->assertSame(
            'hello',
            $mailer->getLastEmailBody()
        );
    }
}
