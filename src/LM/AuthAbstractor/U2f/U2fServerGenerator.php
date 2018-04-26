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

    /**
     * @param IApplicationConfiguration $userConfig
     * @todo Rename $userConfig to $appConfig
     */
    public function __construct(IApplicationConfiguration $userConfig)
    {
        $this->appId = $userConfig->getAppId();
    }

    /**
     * @return Server An instance of Firehed server.
     */
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
