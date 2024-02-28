<?php

namespace ModernMcGuire\MailSpy\Commands;

use Illuminate\Console\Command;

class MailSpyCommand extends Command
{
    public $signature = 'mailspy';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
