<?php

declare(strict_types=1);

namespace Tests\LM;

use InvalidArgumentException;
use LM\AuthAbstractor\Challenge\U2fChallenge;
use LM\AuthAbstractor\Model\ChallengeDefinition;
use PHPUnit\Framework\TestCase;

class ChallengeDefinitionTest extends TestCase
{
    public function testChallengeDefinition()
    {
        $challengeDefinition = new ChallengeDefinition(
            U2fChallenge::class,
            2
        )
        ;
        $this->assertSame(
            U2fChallenge::class,
            $challengeDefinition->getClassname()
        )
        ;
        $this->assertSame(
            2,
            $challengeDefinition->getNumber()
        )
        ;

        $unserialized = unserialize(serialize($challengeDefinition));
        $this->assertSame(
            $challengeDefinition->getClassname(),
            $unserialized->getClassname()
        );
        $this->assertSame(
            $challengeDefinition->getNumber(),
            $unserialized->getNumber()
        );

        $this->expectException(InvalidArgumentException::class);
        new ChallengeDefinition(
            TestCase::class
        )
        ;
    }
}
