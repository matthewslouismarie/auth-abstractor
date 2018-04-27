<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

interface IMailer
{
    public function send(string $to, string $body): void;
}
