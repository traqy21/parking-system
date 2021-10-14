<?php


namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait ProjectTrait
{

    protected function sendEmail($email, $subject, $content, $attachment = null) : void {
        $data = [
            'from_email' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => $subject,
            'email_address' => $email,
            'attachment' => $attachment,
        ];
        Log::debug('data email: ', $data);
        try {
            Mail::send(['html' => 'emails.send'], ['content' => $content], function ($message) use ($data) {
                $message->from($data['from_email'], $data['from_name']);
                $message->to(trim($data['email_address']));
                $message->subject($data['subject']);
                $message->bcc('aries@peachbpo.com');

                if(isset($data['attachment'])){
                    $message->attach($data['attachment']);
                }
            });
            Log::info('Email sent to : ', [$data['email_address']]);

        } catch (\Exception $e) {
            Log::info("Exception : ", [$e->getMessage()]);
            Log::info("Exception Trace: ", [$e->getTraceAsString()]);
        }

    }
}
