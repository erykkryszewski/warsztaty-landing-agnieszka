<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SettingModel;

class SettingsService
{
    private ?array $sectionsCache = null;

    private ?array $valuesCache = null;

    public function __construct(
        private readonly SettingModel $settings,
        private readonly SettingsRegistry $registry,
        private readonly ContentFieldService $fields,
        private readonly UploadService $uploads
    ) {
    }

    public function sections(): array
    {
        if ($this->sectionsCache !== null) {
            return $this->sectionsCache;
        }

        $stored = $this->settings->allIndexed();
        $sections = [];

        foreach ($this->registry->all() as $section) {
            $payload = $this->decode($stored[$section['key']]['content_json'] ?? null);
            $sections[] = array_merge($section, [
                'content' => $this->mergeDefaults($section['fields'], $payload),
            ]);
        }

        $this->sectionsCache = $sections;

        return $this->sectionsCache;
    }

    public function values(): array
    {
        if ($this->valuesCache !== null) {
            return $this->valuesCache;
        }

        $values = [];

        foreach ($this->sections() as $section) {
            $values[$section['key']] = $section['content'];
        }

        $this->valuesCache = $values;

        return $this->valuesCache;
    }

    public function update(array $input, array $files, array $removals = []): array
    {
        $sections = $this->registry->all();
        $currentValues = $this->values();

        $result = $this->fields->fillGroups(
            array_map(static fn (array $section): array => [
                'key' => $section['key'],
                'fields' => $section['fields'],
            ], $sections),
            $input,
            $files,
            $removals,
            $currentValues,
            fn (array $file, array $field, mixed $current): string => $this->uploads->storeImage($file, 'settings', is_string($current) ? $current : null),
            fn (mixed $current) => $this->uploads->removeImage(is_string($current) ? $current : null)
        );

        if ($result['errors'] !== []) {
            return $result;
        }

        foreach ($sections as $section) {
            $this->settings->upsert($section['key'], json_encode($result['data'][$section['key']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $this->sectionsCache = null;
        $this->valuesCache = null;

        return $result;
    }

    private function mergeDefaults(array $fields, array $payload): array
    {
        return array_replace($this->fields->defaultsFromFields($fields), $payload);
    }

    private function decode(?string $json): array
    {
        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
