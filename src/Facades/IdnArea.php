<?php

declare(strict_types=1);

namespace zaidysf\IdnArea\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \zaidysf\IdnArea\IdnArea
 */
class IdnArea extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \zaidysf\IdnArea\IdnArea::class;
    }
}
