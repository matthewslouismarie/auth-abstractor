<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Enum\AuthenticationProcess;

use LM\Common\Enum\AbstractEnum;

/**
 * An enum for the status of an authentication process. It can either be ongoing,
 * succeeded, or failed.
 */
class Status extends AbstractEnum
{
    /** @var string */
    const ONGOING = "ONGOING";

    /** @var string */
    const SUCCEEDED = "SUCCEEDED";

    /** @var string */
    const FAILED = "FAILED";
}
