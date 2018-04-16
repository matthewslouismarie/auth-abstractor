<?php

namespace LM\Authentifier\U2f;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use Firehed\U2F\Server;

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
