<?php

namespace LM\Authentifier\Authentifier;

use LM\Authentifier\Model\DataManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

interface IAuthentifier
{
    public function process(RequestInterface $request, DataManager $dataManager): ResponseInterface;
}
