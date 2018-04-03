<?php

namespace LM\Authentifier\Enum\AuthenticationRequest;

use LM\Common\Enum\AbstractEnum;

class Status extends AbstractEnum
{
    const NOT_STARTED = "NOT_STARTED";

    const ONGOING = "ONGOING";

    const SUCEEDED = "SUCCEEDED";

    const FAILED = "FAILED";
}
