<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Model;

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
}
