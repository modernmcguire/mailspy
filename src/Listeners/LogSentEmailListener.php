<?php

namespace ModernMcGuire\MailSpy\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use ModernMcGuire\MailSpy\Models\Email;

class LogSentEmailListener implements ShouldQueue
{
    public function handle(MessageSent $event)
    {
        /** @var \Symfony\Component\Mime\Email $message */
        $message = $event->message;

        try {
            // Get the email id from the email headers
            $emailId = $message->getHeaders()->get('X-MailSpy-Email-Id');

            if (! $emailId) {
                return;
            }

            $email = Email::where('id', $emailId->getValue())->first();

            $email->update([
                'sent_at' => now(),
            ]);

            $this->registerUserListeners($email);
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function registerUserListeners(Email $email): void {}
}
