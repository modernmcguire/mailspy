<?php

namespace ModernMcGuire\MailSpy\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class EmailContent extends Model
{
    protected $table = 'mailspy_email_content';

    protected $guarded = [];

    public function getConnectionName()
    {
        return config('mailspy.connection');
    }

    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }

    public function html(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                if (config('mailspy.compress')) {
                    try {
                        return gzuncompress($value);
                    } catch (\Exception $e) {
                        // If decompression fails, return the raw value or log the error
                        return $value;
                    }
                }

                return $value;
            },
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }

                return config('mailspy.compress') ? gzcompress($value) : $value;
            },
        );
    }
}
