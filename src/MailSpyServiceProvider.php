<?php

namespace ModernMcGuire\MailSpy;

use ModernMcGuire\MailSpy\Models\Email;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Database\Eloquent\Relations\Relation;
use ModernMcGuire\MailSpy\Listeners\LogSendingEmailListener;
use ModernMcGuire\MailSpy\Listeners\LogSentEmailListener;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailSpyServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        if (config('mailspy.enabled') === false) {
            return;
        }

        return parent::register();
    }

    public function boot()
    {
        if (config('mailspy.enabled') === false) {
            return;
        }

        return parent::boot();
    }

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
                'create_mailspy_email_senders_table',
                'create_mailspy_email_tags_table',
            ])
            ->runsMigrations();
    }

    public function registeringPackage()
    {
        $this->app->singleton(MailSpy::class, function () {
            return new MailSpy();
        });
    }

    public function packageRegistered()
    {
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

    public function packageBooted()
    {
        $this->registerEventListeners();
    }


    /**
     * Register the event listeners
     */
    private function registerEventListeners()
    {
        $events = $this->app['events'];

        $events->listen(
            MessageSending::class,
            LogSendingEmailListener::class
        );

        $events->listen(
            MessageSent::class,
            LogSentEmailListener::class
        );


        foreach (app(MailSpy::class)->getSentListeners() as $listener) {
            $events->listen(
                MessageSent::class,
                function ($event) use ($listener) {
                    $emailId = $event->message->getHeaders()->get('X-MailSpy-Email-Id');
                    $email = Email::where('id', $emailId->getValue())->first();

                    return $listener($event, $email);
                }
            );
        }


        foreach(app(MailSpy::class)->getSendingListeners() as $listener) {
            $events->listen(
                MessageSending::class,
                function ($event) use ($listener) {
                    $emailId = $event->message->getHeaders()->get('X-MailSpy-Email-Id');

                    $email = Email::where('id', $emailId->getValue())->first();

                    return $listener($event, $email);
                }
            );
        }

    }
}
