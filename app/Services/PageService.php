<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PageModel;

class PageService
{
    private ?array $allPagesCache = null;

    private array $pageCache = [];

    private ?array $navigationCache = null;

    public function __construct(
        private readonly PageModel $pages,
        private readonly PageRegistry $registry,
        private readonly ContentFieldService $fields,
        private readonly UploadService $uploads
    ) {
    }

    public function all(): array
    {
        if ($this->allPagesCache !== null) {
            return $this->allPagesCache;
        }

        $stored = $this->pages->allIndexed();
        $pages = [];

        foreach ($this->registry->all() as $definition) {
            $pageKey = $definition['key'];
            $row = $stored[$pageKey] ?? [];
            $pages[] = $this->shapePage($definition, $row);
        }

        $this->allPagesCache = $pages;

        return $this->allPagesCache;
    }

    public function navigation(): array
    {
        if ($this->navigationCache !== null) {
            return $this->navigationCache;
        }

        $this->navigationCache = array_values(array_filter($this->all(), static fn (array $page): bool => (bool) ($page['show_in_navigation'] ?? false)));

        return $this->navigationCache;
    }

    public function find(string $pageKey): ?array
    {
        if (array_key_exists($pageKey, $this->pageCache)) {
            return $this->pageCache[$pageKey];
        }

        $definition = $this->registry->find($pageKey);

        if ($definition === null) {
            return null;
        }

        $row = $this->pages->findByKey($pageKey) ?? [];

        $this->pageCache[$pageKey] = $this->shapePage($definition, $row);

        return $this->pageCache[$pageKey];
    }

    public function findBySlug(string $slug): ?array
    {
        $definition = $this->registry->findBySlug($slug);

        if ($definition === null) {
            return null;
        }

        return $this->find($definition['key']);
    }

    public function update(string $pageKey, array $content, array $files, array $meta, array $removals = []): array
    {
        $page = $this->find($pageKey);

        if ($page === null) {
            return ['data' => [], 'errors' => ['page' => 'Nie znaleziono strony.']];
        }

        $result = $this->fields->fillGroups(
            $page['groups'],
            $content,
            $files,
            $removals,
            $page['content'],
            fn (array $file, array $field, mixed $current): string => $this->uploads->storeImage($file, 'pages', is_string($current) ? $current : null),
            fn (mixed $current) => $this->uploads->removeImage(is_string($current) ? $current : null)
        );

        if ($result['errors'] !== []) {
            return $result;
        }

        $defaults = $this->fields->defaultsFromGroups($page['groups']);
        $normalizedData = $this->repairArrayStrings(
            $this->fields->normalizeGroups($page['groups'], $result['data']),
            $defaults
        );

        $metaImage = $page['meta_image'];
        $metaFiles = is_array($files['seo'] ?? null) ? $files['seo'] : [];
        $removeMetaImage = in_array((string) ($meta['meta_image_remove'] ?? ''), ['1', 'true', 'on', 'yes'], true);

        if ($removeMetaImage) {
            $this->uploads->removeImage($metaImage !== '' ? $metaImage : null);
            $metaImage = '';
        }

        if (is_array($metaFiles['meta_image'] ?? null) && (int) ($metaFiles['meta_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            try {
                $metaImage = $this->uploads->storeImage($metaFiles['meta_image'], 'pages', $metaImage !== '' ? $metaImage : null);
            } catch (\Throwable $exception) {
                return ['data' => $result['data'], 'errors' => ['seo.meta_image' => 'Nie udało się zapisać pliku.']];
            }
        }

        $this->pages->upsert($pageKey, json_encode($normalizedData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), [
            'meta_title' => trim((string) ($meta['meta_title'] ?? '')),
            'meta_description' => trim((string) ($meta['meta_description'] ?? '')),
            'meta_image' => $metaImage,
        ]);

        $this->allPagesCache = null;
        $this->pageCache = [];
        $this->navigationCache = null;

        return ['data' => $normalizedData, 'errors' => []];
    }

    public function repairStoredContent(): void
    {
        foreach ($this->registry->all() as $definition) {
            $pageKey = (string) ($definition['key'] ?? '');

            if ($pageKey === '') {
                continue;
            }

            $row = $this->pages->findByKey($pageKey);

            if ($row === null) {
                continue;
            }

            $decoded = $this->decode($row['content_json'] ?? null);
            $defaults = $this->fields->defaultsFromGroups($definition['groups']);
            $normalized = $this->repairArrayStrings(
                $this->fields->normalizeGroups($definition['groups'], $decoded),
                $defaults
            );
            $encoded = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (!is_string($encoded) || $encoded === (string) ($row['content_json'] ?? '')) {
                continue;
            }

            $this->pages->upsert($pageKey, $encoded, [
                'meta_title' => $row['meta_title'] ?? '',
                'meta_description' => $row['meta_description'] ?? '',
                'meta_image' => $row['meta_image'] ?? '',
            ]);
        }

        $this->allPagesCache = null;
        $this->pageCache = [];
        $this->navigationCache = null;
    }

    private function shapePage(array $definition, array $row): array
    {
        $defaults = $this->fields->defaultsFromGroups($definition['groups']);
        $content = $this->repairArrayStrings(
            $this->fields->normalizeGroups($definition['groups'], $this->decode($row['content_json'] ?? null)),
            $defaults
        );

        return array_merge($definition, [
            'content' => $content,
            'meta_title' => $row['meta_title'] ?? '',
            'meta_description' => $row['meta_description'] ?? '',
            'meta_image' => $row['meta_image'] ?? '',
        ]);
    }

    private function decode(?string $json): array
    {
        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function repairArrayStrings(mixed $value, mixed $defaults): mixed
    {
        if (is_array($value)) {
            $repaired = [];

            foreach ($value as $key => $item) {
                $repaired[$key] = $this->repairArrayStrings(
                    $item,
                    is_array($defaults) ? ($defaults[$key] ?? null) : null
                );
            }

            return $repaired;
        }

        if (is_string($value) && trim($value) === 'Array') {
            if (is_scalar($defaults)) {
                return (string) $defaults;
            }

            return '';
        }

        return $value;
    }
}
