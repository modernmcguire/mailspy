<?php

namespace ModernMcGuire\MailSpy\Listeners;

use Illuminate\Mail\Events\MessageSent;
use ModernMcGuire\MailSpy\Models\Email;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSentEmailListener implements ShouldQueue
{
    public function handle(MessageSent $event)
    {
        // Access the message object
        $message = $event->message;

        try {
            // Get the email id from the email headers
            $emailId = $message->getHeaders()->get('X-MailSpy-Email-Id');

            if(!$emailId) {
                return;
            }

            Email::where('id', $emailId->getValue())->update([
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

    }
}
