<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Exception;

use Exception;

/**
 * This exception is thrown by U2fChallenge when it cannot find any U2F
 * registration associated with the user.
 */
class NoRegisteredU2fTokenException extends Exception
{
}
