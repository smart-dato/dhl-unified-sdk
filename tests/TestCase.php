<?php

namespace SmartDato\DhlUnified\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SmartDato\DhlUnified\DhlUnifiedServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelDataServiceProvider::class,
            DhlUnifiedServiceProvider::class,
        ];
    }
}
