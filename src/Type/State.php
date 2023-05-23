<?php

declare(strict_types=1);

namespace App\Type;

enum State: string
{
    case Draft = 'draft';
    case Published = 'published';
}
