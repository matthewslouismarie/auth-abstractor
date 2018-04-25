<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use UnexpectedValueException;

/**
 * @todo Add one using sessions?
 */
class TestingTokenStorage implements TokenStorageInterface
{
    private $dataDir;

    public function __construct(string $projectRootPath)
    {
        if (!file_exists($projectRootPath.'/tmp') && !mkdir($projectRootPath.'/tmp', 0744)) {
            throw new UnexpectedValueException();
        }
        $this->dataDir = $projectRootPath.'/tmp/'.rand();
        if (!mkdir($this->dataDir)) {
            throw new UnexpectedValueException();
        }
    }

    /**
     * @todo Should check that the folder has been deleted.
     */
    public function __destruct()
    {
        array_map('unlink', glob($this->dataDir.'/*'));
        rmdir($this->dataDir);
    }

    public function getToken($tokenId)
    {
        if (file_exists($this->dataDir.'/'.$tokenId)) {
            return file_get_contents($this->dataDir.'/'.$tokenId);
        } else {
            throw new TokenNotFoundException();
        }
    }

    /**
     * @todo Check for case where file_put_contents returns false.
     */
    public function setToken($tokenId, $token)
    {
        file_put_contents($this->dataDir.'/'.$tokenId, $token);
    }

    public function removeToken($tokenId)
    {
        if (file_exists($this->dataDir.'/'.$tokenId)) {
            return unlink($this->dataDir.'/'.$tokenId);
        } else {
            return;
        }
    }

    public function hasToken($tokenId)
    {
        return file_exists($this->dataDir.'/'.$tokenId);
    }
}
