<?php

declare(strict_types=1);

namespace Tests\LM;

use Exception;
use LM\AuthAbstractor\Controller\AuthenticationKernel;
use LM\AuthAbstractor\Implementation\ApplicationConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @todo Find a better name?
 */
abstract class LibTestCase extends TestCase
{
    private $kernel;

    public function setUp()
    {
        $this->markTestSkipped('Must be updated.');

        $pwdConfig = [
            'min_length' => 5,
            'enforce_min_length' => true,
            'uppercase' => false,
            'special_chars' => false,
            'numbers' => false,
        ];
        $this->kernel = new AuthenticationKernel(new ApplicationConfiguration(
            'https://example.org',
            'https://example.org/assets',
            function (string $username): ?Member {
                throw new Exception('Unsupported yet');
            }
        ));
    }

    public function getKernel(): AuthenticationKernel
    {
        return $this->kernel;
    }
}
