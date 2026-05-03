<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('【ココケア】メールアドレスの確認')
            ->greeting('こんにちは！')
            ->line('「ココケア」にご登録いただきありがとうございます。')
            ->line('下のボタンをクリックして、メールアドレスの確認を完了してください。')
            ->action('メールアドレスを確認する', $url)
            ->line('このメールにお心当たりがない場合は、このままメールを破棄してください。特別な操作は必要ありません。')
            ->salutation("よろしくお願いいたします。\nココケア");
    }
}
