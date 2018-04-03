<?php

namespace LM\Authentifier\Enum\AuthenticationRequset;

use LM\Authentifier\Enum\AbstractEnum;

class Status implements AbstractEnum
{
    const NOT_STARTED = "NOT_STARTED";

    const ONGOING = "ONGOING";

    const SUCEEDED = "SUCEEDED";

    const FAILED = "FAILED";
}
