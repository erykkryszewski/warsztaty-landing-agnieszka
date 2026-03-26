<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContactMessageModel;

class ContactFormService
{
    public function __construct(
        private readonly ContactMessageModel $messages,
        private readonly MailService $mail,
        private readonly RateLimiterService $rateLimiter
    ) {
    }

    public function submit(array $data, string $ipAddress, string $userAgent): array
    {
        $rateLimitKey = 'contact-form:' . $ipAddress;

        if ($this->rateLimiter->tooManyAttempts($rateLimitKey, 5, 900)) {
            return ['errors' => ['form' => 'Za dużo prób wysyłki. Spróbuj ponownie za kilkanaście minut.']];
        }

        $errors = [];

        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $subject = trim((string) ($data['subject'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));
        $honeypot = trim((string) ($data['website'] ?? ''));

        if ($honeypot !== '') {
            $errors['form'] = 'Nie udało się wysłać formularza.';
        }

        if ($name === '') {
            $errors['name'] = 'Podaj imię i nazwisko.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Podaj poprawny adres e-mail.';
        }

        if ($message === '') {
            $errors['message'] = 'Wpisz wiadomość.';
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        $this->rateLimiter->hit($rateLimitKey, 900);

        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ];

        $this->messages->create($payload);

        $body = "Nowa wiadomość z formularza kontaktowego\n\n"
            . "Imię i nazwisko: {$name}\n"
            . "E-mail: {$email}\n"
            . "Telefon: {$phone}\n"
            . "Temat: {$subject}\n\n"
            . $message . "\n";

        $delivery = $this->mail->send('Nowa wiadomość ze strony', $body, $email);

        return ['errors' => [], 'delivery' => $delivery];
    }
}
