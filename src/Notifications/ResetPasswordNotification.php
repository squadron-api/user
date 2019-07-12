<?php

namespace Squadron\User\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * {@inheritdoc}
     */
    public function toMail($notifiable)
    {
        $emailView = config('squadron.user.restore.emailView');
        $url = sprintf('%s/#%s&&%s', config('app.urlRestore'), $notifiable->email, $this->token);

        if ($emailView === null)
        {
            $appName = config('app.name');

            return (new MailMessage())
                ->subject('Reset Password Notification')
                ->line('Use this link to reset your password. The link is only valid for 24 hours.')
                ->line('')
                ->line('************')
                ->line("Hi {$notifiable->firstName},")
                ->line('************')
                ->line('')
                ->line("You recently requested to reset your password for your {$appName} account. Use the button below to reset it. This password reset is only valid for the next 24 hours.")
                ->line('')
                ->action('Reset your password', $url)
                ->line('')
                ->line('If you did not request a password reset, please ignore this email or contact support if you have questions.')
                ->line('')
                ->line('Thanks,')
                ->line("The {$appName} Team")
                ->line('')
                ->line('If you’re having trouble with the button above, copy and paste the URL below into your web browser.')
                ->line('')
                ->line($url)
                ->line('')
                ->line('© 2019 Just Hatched. All rights reserved.');
        }

        return (new MailMessage())
            ->subject('Reset Password Notification')
            ->view($emailView, [
                'name' => $notifiable->firstName,
                'url' => $url,
            ]);
    }
}
