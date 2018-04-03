<?php

namespace LM\Authentifier\AuthenticationRequestController;

/**
 * @todo Is it better to delegate to the library used the responsability of
 * storing and retrieving correctly the TransitingDataManager object or the
 * implementation of the storage mechanism?
 */
class AuthenticationRequestController
{
    public function processRequest(TransitingDataManager $tdm): TransitingDataManager
    {

    }
}