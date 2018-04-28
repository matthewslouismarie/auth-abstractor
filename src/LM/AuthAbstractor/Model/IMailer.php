<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

/**
 * Interface for a mailer.
 */
interface IMailer
{
    /**
     * @param string $to The recipient.
     * @param string $body The content of they email.
     */
    public function send(string $to, string $body): void;
}
