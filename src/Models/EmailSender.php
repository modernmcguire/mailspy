<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSender extends Model
{
    protected $table = 'mailspy_email_senders';

    protected $guarded = [];

    public function getConnectionName()
    {
        return config('mailspy.connection');
    }

    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }
}
