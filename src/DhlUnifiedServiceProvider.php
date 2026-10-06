<?php

namespace SmartDato\DhlUnified;

use SmartDato\DhlUnified\Connectors\DhlUnifiedConnector;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DhlUnifiedServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('dhl-unified-sdk')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(DhlUnifiedConnector::class, fn (): DhlUnifiedConnector => new DhlUnifiedConnector(
            apiKey: (string) config('dhl-unified-sdk.api_key'),
            baseUrl: config('dhl-unified-sdk.base_url') ?? DhlUnifiedConnector::PRODUCTION_URL,
            delayInMilliseconds: (int) config('dhl-unified-sdk.delay_ms', 0),
        ));

        $this->app->singleton(DhlUnified::class, fn ($app): DhlUnified => new DhlUnified(
            connector: $app->make(DhlUnifiedConnector::class),
        ));
    }
}
