<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LM\AuthAbstractor\Implementation\Callback;
use Psr\Http\Message\ResponseInterface;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\Common\DataStructure\TypedMap;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\AuthenticationProcess;

class CallbackTest extends TestCase
{
    public function test()
    {
        $failureResponse = (new DiactorosFactory())->createResponse(
            new Response('FAILURE')
        );
        $successResponse = (new DiactorosFactory())->createResponse(
            new Response('SUCCESS')
        );
        $callback = new Callback(
            function (IAuthenticationProcess $process) use ($failureResponse): ResponseInterface {
                return $failureResponse;
            },
            function (IAuthenticationProcess $process) use ($successResponse): ResponseInterface {
                return $successResponse;
            }
        );
        $process = new AuthenticationProcess(new TypedMap());
        $this->assertSame(
            $failureResponse,
            $callback->handleFailedProcess($process)
        );
        $this->assertSame(
            $successResponse,
            $callback->handleSuccessfulProcess($process)
        );
    }
}
