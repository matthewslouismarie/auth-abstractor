<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use InvalidArgumentException;
use LM\AuthAbstractor\Challenge\IChallenge;
use Serializable;

/**
 * Not used yet. Might be in the future.
 *
 * Currently challenges are specified when instantiating the authentication
 * process as an array FQCN strings. However, sometimes, we want to assign data
 * to each challenge (e.g. the maximum number of attempts). Storing challenge
 * definitions instead might be a solution.
 *
 * @ignore
 * @todo Will later be used to specify a maximum number of attempts per
 * challenge.
 */
class ChallengeDefinition implements Serializable
{
    /** @var string */
    private $classname;

    /** @var int */
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
