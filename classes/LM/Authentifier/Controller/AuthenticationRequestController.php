<?php

namespace LM\Authentifier\Controller;

use GuzzleHttp\Psr7\Response;
use LM\Authentifier\Model\AuthenticationRequest;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Enum\AuthenticationRequest\Status;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo Is it better to delegate to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    private $response;

    public function __construct(AuthenticationRequest $authRequest)
    {
        $status = $authRequest->getStatus();
        if ($status->is(Status::NOT_STARTED)) {
            $this->response = new Response(200, [], "Hello you");
        } else if ($status->is(Status::ONGOING)) {
            $this->response = new Response(200, [], "Ongoing");
        } else if ($status->is(Status::SUCCEEDED)) {
            $this->response = new Response(200, [], "Succeeded");
        } else if ($status->is(Status::FAILED)) {
            $this->response = new Response(200, [], "Failed");
        } else {
            throw new UnexpectedValueException();
        }
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getDataManager(): DataManager
    {

    }
}
