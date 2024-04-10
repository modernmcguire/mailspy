<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTag extends Model
{
    protected $table = 'mailspy_email_tags';

    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }


}
