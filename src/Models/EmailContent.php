<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Model;

class EmailContent extends Model
{
    protected $table = 'mailspy_email_content';

    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }
}
