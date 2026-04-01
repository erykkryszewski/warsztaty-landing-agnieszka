<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Core\Request;
use App\Http\Controllers\Controller;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    public function edit(Request $request): \App\Core\Response
    {
        $sections = $this->app->make(SettingsService::class)->sections();
        $old = $this->pullOldInput();

        if (isset($old['settings']) && is_array($old['settings'])) {
            foreach ($sections as &$section) {
                if (isset($old['settings'][$section['key']]) && is_array($old['settings'][$section['key']])) {
                    $section['content'] = array_replace_recursive($section['content'], $old['settings'][$section['key']]);
                }

                if (isset($old['settings_remove'][$section['key']]) && is_array($old['settings_remove'][$section['key']])) {
                    foreach ($old['settings_remove'][$section['key']] as $fieldKey => $remove) {
                        if (in_array((string) $remove, ['1', 'true', 'on', 'yes'], true)) {
                            $section['content'][$fieldKey] = '';
                        }
                    }
                }
            }
            unset($section);
        }

        return $this->renderAdmin('admin/settings/edit', [
            'sections' => $sections,
        ]);
    }

    public function update(Request $request): \App\Core\Response
    {
        $result = $this->app->make(SettingsService::class)->update(
            $request->input('settings', []),
            $request->file('settings_files') ?? [],
            $request->input('settings_remove', [])
        );

        if (($result['errors'] ?? []) !== []) {
            return $this->validationRedirect($result['errors'], $request->all(), '/admin/settings/');
        }

        $this->rebuildDeployPackage();

        return $this->redirect('/admin/settings/', 'Zapisano zmiany.');
    }
}
