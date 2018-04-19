<?php

declare(strict_types=1);

namespace LM\Authentifier\Model;

use InvalidArgumentException;
use LM\Authentifier\Challenge\IChallenge;
use Serializable;

/**
 * @todo Will later be used to specify a maximum number of attempts per
 * challenge.
 * Not used yet. Might be in the future.
 */
class ChallengeDefinition implements Serializable
{
    private $classname;

    private $number;

    public function __construct(string $classname, int $number = 1)
    {
        if (!in_array(IChallenge::class, class_implements($classname), true)) {
            throw new InvalidArgumentException();
        } elseif ($number < 1) {
            throw new InvalidArgumentException();
        }
        $this->classname = $classname;
        $this->number = $number;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function serialize()
    {
        return serialize([
            $this->classname,
            $this->number,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->classname,
            $this->number) = unserialize($serialized);
    }
}
