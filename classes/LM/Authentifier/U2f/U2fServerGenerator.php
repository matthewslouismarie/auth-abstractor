<?php

namespace LM\Authentifier\U2f;

use LM\Authentifier\Configuration\IConfiguration;
use Firehed\U2F\Server;
use Symfony\Component\DependencyInjection\ContainerInterface;

class U2fServerGenerator
{
    private $appId;

    public function __construct(IConfiguration $userConfig)
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
