<?php

namespace LM\Authentifier\Controller;

use LM\Authentifier\Configuration\IConfiguration;
use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo Is it better to delegate to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    private $httpResponse;

    /**
     * @todo Current authentifier stored in request?
     * @todo Request handling shouldn't be in construct().
     */
    public function __construct(
        RequestInterface $httpRequest,
        AuthenticationRequest $authRequest)
    {
        $status = $authRequest->getStatus();
        if ($status->is(Status::ONGOING)) {
            $authentifier = $authRequest->getCurrentAuthentifier();
            $this->httpResponse = $authentifier->process($httpRequest);

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
