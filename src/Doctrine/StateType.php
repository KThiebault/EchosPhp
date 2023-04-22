<?php

namespace App\Doctrine;

use App\Type\State;

class StateType extends AbstractEnumType
{
    public const STATE = 'state';

    public static function getEnumsClass(): string
    {
        return State::class;
    }

    public function getName(): string
    {
        return self::STATE;
    }
}