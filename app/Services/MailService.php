<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class MailService
{
    public function __construct(private readonly Application $app)
    {
    }

    public function send(string $subject, string $body, string $replyTo): string
    {
        $to = (string) $this->app->config('mail.to');
        $from = (string) $this->app->config('mail.from');
        $fromName = (string) $this->app->config('mail.from_name');
        $safeReplyTo = filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : $from;

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . sprintf('%s <%s>', $this->cleanHeaderValue($fromName), $this->cleanHeaderValue($from)),
            'Reply-To: ' . $this->cleanHeaderValue($safeReplyTo),
        ];

        $sent = function_exists('mail') ? @mail($to, $subject, $body, implode("\r\n", $headers)) : false;

        if ($sent) {
            return 'sent';
        }

        $logPath = $this->app->basePath('storage/logs/mail.log');
        $logBody = sprintf("[%s]\nTO: %s\nSUBJECT: %s\n%s\n\n", date('Y-m-d H:i:s'), $to, $subject, $body);
        file_put_contents($logPath, $logBody, FILE_APPEND);

        return 'logged';
    }

    private function cleanHeaderValue(string $value): string
    {
        return str_replace(["\r", "\n"], '', trim($value));
    }
}
