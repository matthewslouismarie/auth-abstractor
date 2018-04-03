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
        switch ($authRequest->getStatus()) {
            case Status::NOT_STARTED:
                $this->response = new Response(200, [], "Hello you");
                break;

            case Status::ONGOING:
                break;

            case Status::SUCCEEDED:
                break;

            case Status::FAILED;
                break;

            default:
                // should never happens, means the system is at fault, error in
                // the code
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
