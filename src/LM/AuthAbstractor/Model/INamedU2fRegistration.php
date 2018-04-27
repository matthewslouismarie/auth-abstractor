<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

interface INamedU2fRegistration extends IU2fRegistration
{
    public function getName(): string;
}
