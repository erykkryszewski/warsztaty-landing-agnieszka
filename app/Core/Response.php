<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public function __construct(
        private readonly string $content = '',
        private readonly int $status = 200,
        private readonly array $headers = []
    ) {
    }

    public static function html(string $content, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] ??= 'text/html; charset=UTF-8';

        return new self($content, $status, $headers);
    }

    public static function text(string $content, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] ??= 'text/plain; charset=UTF-8';

        return new self($content, $status, $headers);
    }

    public static function xml(string $content, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] ??= 'application/xml; charset=UTF-8';

        return new self($content, $status, $headers);
    }

    public static function redirect(string $url, int $status = 302): self
    {
        return new self('', $status, ['Location' => $url]);
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->securityHeaders() as $name => $value) {
            header($name . ': ' . $value);
        }

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->content;
    }

    private function securityHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
                "img-src 'self' data: https:",
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]),
        ];
    }
}
