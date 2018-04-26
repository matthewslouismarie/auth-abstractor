<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Exception;

use Exception;

/**
 * This exception is thrown by the kernel when it is asked to process an HTTP
 * request with an authentication process already finished.
 *
 * @see \LM\AuthAbstractor\Controller\AuthenticationKernel
 */
class FinishedProcessException extends Exception
{
}
