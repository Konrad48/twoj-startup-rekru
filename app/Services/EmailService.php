<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendWelcomeEmails(User $user)
    {
        $subject = 'Wiadomość powitalna';
        $content = "Witamy użytkownika {$user->first_name} {$user->last_name}";

        foreach ($user->emails as $emailRecord) {
            Mail::raw($content, function ($message) use ($emailRecord, $subject) {
                $message->to($emailRecord->email)
                        ->subject($subject);
            });
        }
    }
}