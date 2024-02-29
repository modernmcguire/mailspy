<?php

namespace ModernMcGuire\MailSpy;

use Illuminate\Mail\Events\MessageSent;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Mail\Events\MessageSending;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ModernMcGuire\MailSpy\Listeners\LogSentEmailListener;
use ModernMcGuire\MailSpy\Listeners\LogSendingEmailListener;

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
            ->hasMigrations([
                'create_mailspy_emails_table',
                'create_mailspy_email_content_table',
                'create_mailspy_email_recipients_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered()
    {
        if(config('mailspy.enabled') === false) {
            return;
        }

        $this->app['events']->listen(
            MessageSending::class,
            LogSendingEmailListener::class
        );

        $this->app['events']->listen(
            MessageSent::class,
            LogSentEmailListener::class
        );

        // if running horizon, add this to the silenced jobs list
        if (class_exists(\Laravel\Horizon\HorizonServiceProvider::class)) {
            $silenced = $this->app['config']->get('horizon.silenced', []);

            $silenced = array_merge($silenced, [
                \ModernMcGuire\MailSpy\Listeners\LogSendingEmailListener::class,
                \ModernMcGuire\MailSpy\Listeners\LogSentEmailListener::class,
            ]);

            $this->app['config']->set('horizon.silenced', $silenced);
        }
    }
}
