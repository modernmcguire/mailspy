<?php

namespace ModernMcGuire\MailSpy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ModernMcGuire\MailSpy\MailSpy
 */
class MailSpy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ModernMcGuire\MailSpy\MailSpy::class;
    }
}
