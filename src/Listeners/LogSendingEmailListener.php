<?php

namespace ModernMcGuire\MailSpy\Listeners;

use Illuminate\Mail\Events\MessageSending;
use ModernMcGuire\MailSpy\Models\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Header\TagHeader;

class LogSendingEmailListener
{
    public function handle(MessageSending $event)
    {
        // Access the message object
        $message = $event->message;

        try {
            // Log the email details. You can customize this part to log whatever you need.
            $email = Email::create([
                'subject' => $message->getSubject(),
            ]);

            // add email id to the email headers
            $message->getHeaders()->addTextHeader('X-MailSpy-Email-Id', $email->id);

            dispatch(function () use ($email, $message) {
                $this->saveSenders($email, $message);
                $this->saveRecipients($email, $message);
                $this->saveContent($email, $message);
                $this->saveTags($email, $message);
            });
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function saveRecipients($email, $message)
    {
        $recipients = collect($message->getTo())->map(function ($recipient) {
            return ['email_address' => $recipient->getAddress()];
        });

        $email->recipients()->createMany($recipients->toArray());
    }

    private function saveSenders($email, \Symfony\Component\Mime\Email $message): void
    {
        /** @var Address $sender */
        collect($message->getReplyTo())->each(function ($sender) use ($email) {
            $email->tags()->create([
                'tag' => 'sender',
                'value' => $sender->getAddress(),
            ]);

            $email->sender()->create([
                'email_address' => $sender->getAddress(),
            ]);
        });
    }

    private function saveContent($email, $message)
    {
        $html = null;
        $text = null;

        // Check if the Email body is multipart
        if ($message->getBody() instanceof \Symfony\Component\Mime\Part\Multipart\AlternativePart) {
            $parts = $message->getBody()->getParts();
            foreach ($parts as $part) {
                if ($part instanceof \Symfony\Component\Mime\Part\TextPart) {
                    // Check the media type to distinguish between plain text and HTML
                    if ($part->getMediaType() === 'text' && $part->getMediaSubtype() === 'html') {
                        $htmlContent = $part->bodyToString();
                        // Decode if quoted-printable
                        $html = quoted_printable_decode($htmlContent);
                    } elseif ($part->getMediaType() === 'text' && $part->getMediaSubtype() === 'plain') {
                        $textContent = $part->bodyToString();
                        // Decode if quoted-printable
                        $text = quoted_printable_decode($textContent);
                    }
                }
            }
        } elseif ($message->getBody() instanceof \Symfony\Component\Mime\Part\TextPart) {
            // For emails with only one part, determine if it's text or HTML
            $part = $message->getBody();
            if ($part->getMediaType() === 'text' && $part->getMediaSubtype() === 'html') {
                $htmlContent = $part->bodyToString();
                // Decode if quoted-printable
                $html = quoted_printable_decode($htmlContent);
            } elseif ($part->getMediaType() === 'text' && $part->getMediaSubtype() === 'plain') {
                $textContent = $part->bodyToString();
                // Decode if quoted-printable
                $text = quoted_printable_decode($textContent);
            }
        }

        $email->content()->create([
            'html' => $html,
            'text' => $text,
        ]);
    }

    private function saveTags(Email $email, \Symfony\Component\Mime\Email $message): void
    {
        /** @var TagHeader $header */
        $header = $message->getHeaders()->get('X-Tag');

        // if method tags exists
        foreach(json_decode($header->getValue(), true) as $tag => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $email->tags()->create([
                'tag' => $tag,
                'value' => $value,
            ]);
        }
    }
}
