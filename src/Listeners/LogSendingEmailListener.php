<?php

namespace ModernMcGuire\MailSpy\Listeners;

use Illuminate\Mail\Events\MessageSending;
use ModernMcGuire\MailSpy\Models\Email;
use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mime\Email as SymfonyEmail;

class LogSendingEmailListener
{
    public function handle(MessageSending $event)
    {
        /** @var SymfonyEmail $message */
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

    private function saveRecipients(Email $email, SymfonyEmail $message)
    {
        // TO:
        $email->recipients()->createMany(
            array_map(function ($recipient) {
                return ['email_address' => $recipient->getAddress()];
            }, $message->getTo())
        );

        // CC:
        $email->recipients()->createMany(
            array_map(function ($recipient) {
                return ['email_address' => $recipient->getAddress()];
            }, $message->getCc())
        );

        // BCC:
        $email->recipients()->createMany(
            array_map(function ($recipient) {
                return ['email_address' => $recipient->getAddress()];
            }, $message->getBcc())
        );
    }

    private function saveSenders(Email $email, SymfonyEmail $message): void
    {
        /* @var Address $sender */
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

    private function saveContent(Email $email, SymfonyEmail $message)
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

    private function saveTags(Email $email, SymfonyEmail $message): void
    {
        /** @var TagHeader $header */
        $header = $message->getHeaders()->get('X-Tag');

        if (! $header) {
            return;
        }

        // if method tags exists
        foreach (json_decode($header->getValue(), true) as $tag => $value) {
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
