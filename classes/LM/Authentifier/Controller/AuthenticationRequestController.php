<?php

namespace LM\Authentifier\Controller;

use DI\Container;
use DI\ContainerBuilder;
use LM\Authentifier\Authentifier\IAuthentifier;
use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use Twig_Environment;
use Twig_Function;
use Twig_Loader_Filesystem;

/**
 * @todo Is it better to delegatehttpRequest to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    private $httpResponse;

    /**
     * @todo Current authentifier stored in request?
     * @todo Request handling shouldn't be in construct().
     * @todo Should check type before instantiating authentifier.
     */
    public function __construct(
        RequestInterface $httpRequest,
        AuthenticationRequest $authRequest)
    {
        // Twig
        $loader = new Twig_Loader_Filesystem(__DIR__."/../../../../templates");
        $twig = new Twig_Environment($loader, [
            "cache" => false,
        ]);
        $assetFunction = new Twig_Function("asset", [$authRequest->getConfiguration(), "getAssetUri"]);
        $twig->addFunction($assetFunction);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            IConfiguration::class => function () use ($authRequest) {
                return $authRequest->getConfiguration();
            },
            Twig_Environment::class => function() use ($twig) {
                return $twig;
            }
        ]);
        $container = $containerBuilder->build();
        $status = $authRequest->getStatus();
        if ($status->is(Status::ONGOING)) {
            if (!$container->get($authRequest->getCurrentAuthentifier()) instanceof IAuthentifier) {
                throw new Exception();
            }
            $authentifier = $container->get($authRequest->getCurrentAuthentifier());

            $this->httpResponse = $authentifier->process($httpRequest, $authRequest->getDataManager());

        } else if ($status->is(Status::SUCCEEDED)) {
            $this->response = new Response(200, [], "Succeeded");
        } else if ($status->is(Status::FAILED)) {
            $this->response = new Response(200, [], "Failed");
        } else {
            throw new UnexpectedValueException();
        }
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }

    public function getDataManager(): DataManager
    {

    }
}
