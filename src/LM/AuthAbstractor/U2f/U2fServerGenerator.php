<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\U2f;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use Firehed\U2F\Server;

/**
 * This class is only a factory to create a Firehed Server object.
 *
 * @internal
 */
class U2fServerGenerator
{
    private $appId;

    public function __construct(IApplicationConfiguration $userConfig)
    {
        $this->appId = $userConfig->getAppId();
    }

    public function getServer(): Server
    {
        $server = new Server();
        $server
            ->disableCAVerification()
            ->setAppId($this->appId)
        ;

        return $server;
    }
}
