<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Email extends Model
{
    protected $table = 'mailspy_emails';

    protected $guarded = [];

    public function content()
    {
        return $this->hasOne(EmailContent::class, 'email_id');
    }

    public function recipients()
    {
        return $this->hasMany(EmailRecipient::class, 'email_id');
    }

    public function tags()
    {
        return $this->hasMany(EmailTag::class, 'email_id');
    }

    public function sender()
    {
        return $this->hasOne(EmailSender::class, 'email_id');
    }
}
