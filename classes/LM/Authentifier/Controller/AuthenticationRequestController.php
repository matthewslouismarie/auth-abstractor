<?php

namespace LM\Authentifier\Controller;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo Is it better to delegate to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    private $response;

    public function __construct(DataManager $tdm)
    {
        $this->response = new Response(200, [], 'Hello');
    }

    public function getResponse(): ResponseInterface
    {
        return $response;
    }

    public function getDataManager(): DataManager
    {

    }
}
