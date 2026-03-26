<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Application;

class UploadService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'ico'];

    public function __construct(private readonly Application $app)
    {
    }

    public function storeImage(array $file, string $directory = 'general', ?string $currentPath = null): string
    {
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $size = (int) ($file['size'] ?? 0);

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException('Unsupported file type.');
        }

        if ($size > 4 * 1024 * 1024) {
            throw new \RuntimeException('File is too large.');
        }

        $mime = mime_content_type((string) $file['tmp_name']) ?: '';

        if ($extension !== 'svg' && !str_starts_with($mime, 'image/')) {
            throw new \RuntimeException('Invalid uploaded file.');
        }

        if ($extension === 'svg') {
            $contents = file_get_contents((string) $file['tmp_name']);

            if (!is_string($contents) || !$this->isSafeSvg($contents)) {
                throw new \RuntimeException('Invalid SVG file.');
            }
        }

        $targetDirectory = $this->app->basePath('public/uploads/' . trim($directory, '/'));

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $filename = date('YmdHis') . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetPath = $targetDirectory . '/' . $filename;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Unable to move uploaded file.');
        }

        $this->removeImage($currentPath);

        return '/uploads/' . trim($directory, '/') . '/' . $filename;
    }

    public function removeImage(?string $path): void
    {
        if (!is_string($path) || !str_starts_with($path, '/uploads/')) {
            return;
        }

        $absolutePath = $this->app->basePath('public' . $path);

        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }

    private function isSafeSvg(string $contents): bool
    {
        if (stripos($contents, '<svg') === false) {
            return false;
        }

        $blockedPatterns = [
            '/<script\b/i',
            '/<foreignObject\b/i',
            '/<iframe\b/i',
            '/<object\b/i',
            '/<embed\b/i',
            '/on[a-z]+\s*=/i',
            '/javascript:/i',
            '/data\s*:\s*text\/html/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $contents) === 1) {
                return false;
            }
        }

        return true;
    }
}
