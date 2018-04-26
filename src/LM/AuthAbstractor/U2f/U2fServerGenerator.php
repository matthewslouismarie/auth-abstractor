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
    /** @var IApplicationConfiguration */
    private $appConfig;

    /**
     * @param IApplicationConfiguration $appConfig
     */
    public function __construct(IApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @return Server An instance of Firehed server.
     */
    public function getServer(): Server
    {
        $server = new Server();
        if (null === $this->appConfig->getU2fCertificates()) {
            $server->disableCAVerification();
        } else {
            $server->setTrustedCAs($this->appConfig->getU2fCertificates());
        }
        $server->setAppId($this->appConfig->getAppId());

        return $server;
    }
}
