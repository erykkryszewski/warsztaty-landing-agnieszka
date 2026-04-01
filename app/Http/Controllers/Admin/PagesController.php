<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\PageService;

class PagesController extends Controller
{
    public function index(Request $request): \App\Core\Response
    {
        return $this->renderAdmin('admin/pages/index', [
            'pages' => $this->app->make(PageService::class)->all(),
        ]);
    }

    public function edit(Request $request, string $pageKey): \App\Core\Response
    {
        $page = $this->app->make(PageService::class)->find($pageKey);

        if ($page === null) {
            return $this->redirect('/admin/pages/', null, 'Nie znaleziono strony.');
        }

        $old = $this->pullOldInput();

        if (isset($old['content']) && is_array($old['content'])) {
            $page['content'] = array_replace_recursive($page['content'], $old['content']);
        }

        if (isset($old['content_remove']) && is_array($old['content_remove'])) {
            foreach ($old['content_remove'] as $groupKey => $groupRemovals) {
                if (!is_array($groupRemovals)) {
                    continue;
                }

                foreach ($groupRemovals as $fieldKey => $remove) {
                    if (in_array((string) $remove, ['1', 'true', 'on', 'yes'], true)) {
                        $page['content'][$groupKey][$fieldKey] = '';
                    }
                }
            }
        }

        if (isset($old['seo']) && is_array($old['seo'])) {
            $page['meta_title'] = $old['seo']['meta_title'] ?? $page['meta_title'];
            $page['meta_description'] = $old['seo']['meta_description'] ?? $page['meta_description'];
            if (in_array((string) ($old['seo']['meta_image_remove'] ?? ''), ['1', 'true', 'on', 'yes'], true)) {
                $page['meta_image'] = '';
            }
        }

        return $this->renderAdmin('admin/pages/edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, string $pageKey): \App\Core\Response
    {
        $result = $this->app->make(PageService::class)->update(
            $pageKey,
            $request->input('content', []),
            $request->file('content_files') ?? [],
            $request->input('seo', []),
            $request->input('content_remove', [])
        );

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/pages/' . $pageKey . '/');
        }

        $this->rebuildDeployPackage();

        return $this->redirect('/admin/pages/' . $pageKey . '/', 'Zapisano zmiany.');
    }
}
