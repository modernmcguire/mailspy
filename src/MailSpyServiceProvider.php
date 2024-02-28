<?php

namespace ModernMcGuire\MailSpy;

use ModernMcGuire\MailSpy\Commands\MailSpyCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailSpyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mailspy')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_mailspy_table')
            ->hasCommand(MailSpyCommand::class);
    }
}
