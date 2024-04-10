<?php

namespace ModernMcGuire\MailSpy\Concerns;

trait MailspyTags
{

    abstract public function tags(): array;

    protected function prepareMailableForDelivery()
    {
        $this->tag(json_encode($this->tags()));

        parent::prepareMailableForDelivery();
    }

}