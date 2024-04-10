<?php

namespace ModernMcGuire\MailSpy;

class MailSpy
{
    private array $sendingListeners = [];

    private array $sentListeners = [];

    public function sending(\Closure $listener)
    {
        $this->sendingListeners[] = $listener;
    }

    public function sent(\Closure $listener)
    {
        $this->sentListeners[] = $listener;
    }

    public function getSendingListeners(): array
    {
        return $this->sendingListeners;
    }

    public function getSentListeners(): array
    {
        return $this->sentListeners;
    }
}
