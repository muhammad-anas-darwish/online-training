<?php

namespace App\Contracts;

interface DTOInterface
{
    public static function fromRequest(array $array): self;
}