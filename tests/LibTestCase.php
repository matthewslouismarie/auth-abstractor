<?php

namespace Tests\LM;

use Exception;
use LM\Authentifier\Controller\AuthenticationKernel;
use LM\Authentifier\Implementation\ApplicationConfiguration;
use LM\Authentifier\Implementation\TestingTokenStorage;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @todo Find a better name?
 */
abstract class LibTestCase extends TestCase
{
    private $kernel;

    public function setUp()
    {
        $pwdConfig = [
            'min_length' => 5,
            'enforce_min_length' => true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
        $this->kernel = new AuthenticationKernel(new ApplicationConfiguration(
            'https://example.org',
            'https://example.org',
            realpath(__DIR__.'/../vendor'),
            null,
            null,
            function (string $username): bool {
                throw new Exception('Unsupported yet');
            },
            realpath(__DIR__.'/..'),
            function (string $username): Member {
                throw new Exception('Unsupported yet');
            },
            $pwdConfig,
            new TestingTokenStorage(realpath(__DIR__.'/..')),
            function (string $username): array {
                throw new Exception('Unsupported yet');
            }
        ));
    }

    public function get(string $serviceId)
    {
        return $this
            ->kernel
            ->getContainer()
            ->get($serviceId)
        ;
    }

    public function getKernel(): AuthenticationKernel
    {
        return $this->kernel;
    }
}
