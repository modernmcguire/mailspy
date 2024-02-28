<?php

namespace ModernMcGuire\MailSpy\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use ModernMcGuire\MailSpy\MailSpyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ModernMcGuire\\MailSpy\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MailSpyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_mailspy_table.php.stub';
        $migration->up();
        */
    }
}
