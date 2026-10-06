<?php

namespace SmartDato\DhlUnified\Facades;

use Illuminate\Support\Facades\Facade;
use SmartDato\DhlUnified\Resources\LocationsResource;

/**
 * @method static LocationsResource locations()
 *
 * @see \SmartDato\DhlUnified\DhlUnified
 */
class DhlUnified extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\DhlUnified\DhlUnified::class;
    }
}
