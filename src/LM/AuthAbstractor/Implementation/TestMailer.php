<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IMailer;

class TestMailer implements IMailer
{
    /** @var string */
    private $lastEmailBody;

    public function send(string $to, string $body): void
    {
        $this->lastEmailBody = $body;
    }

    public function getLastEmailBody(): string
    {
        return $this->lastEmailBody;
    }
}
