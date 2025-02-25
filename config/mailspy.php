<?php

// config for ModernMcGuire/MailSpy
return [
    /**
     * Enable or disable MailSpy. Default: true
     */
    'enabled' => env('MAILSPY_ENABLED', true),

    /**
     * The Database connection to use for the MailSpy models. Default: null
     */
    'connection' => env('MAILSPY_CONNECTION', null),

    /**
     * Whether or not we should compress the html contents in the database. Default: false
     */
    'compress' => env('MAILSPY_COMPRESSION_ENABLED', false),
];
