<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Application;
use App\Models\BlogPostModel;
use App\Models\PageModel;
use App\Models\PluginStateModel;
use App\Models\SettingModel;
use App\Models\UserModel;
use App\Services\ContentFieldService;
use App\Services\PageRegistry;
use App\Services\PluginManager;
use App\Services\SettingsRegistry;

class DatabaseSeeder
{
    public function run(Application $app): void
    {
        $this->seedSettings($app);
        $this->seedPages($app);
        $this->seedPlugins($app);
        $this->seedAdminUser($app);
        $this->seedBlogPosts($app);
    }

    private function seedSettings(Application $app): void
    {
        $fields = $app->make(ContentFieldService::class);
        $registry = $app->make(SettingsRegistry::class);
        $model = $app->make(SettingModel::class);
        $existing = $model->allIndexed();

        foreach ($registry->all() as $section) {
            if (isset($existing[$section['key']])) {
                continue;
            }

            $defaults = $fields->defaultsFromFields($section['fields']);
            $model->upsert($section['key'], json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    private function seedPages(Application $app): void
    {
        $fields = $app->make(ContentFieldService::class);
        $registry = $app->make(PageRegistry::class);
        $model = $app->make(PageModel::class);
        $existing = $model->allIndexed();

        foreach ($registry->all() as $page) {
            if (isset($existing[$page['key']])) {
                continue;
            }

            $defaults = $fields->defaultsFromGroups($page['groups']);
            $model->upsert($page['key'], json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), [
                'meta_title' => '',
                'meta_description' => '',
                'meta_image' => '',
            ]);
        }
    }

    private function seedPlugins(Application $app): void
    {
        $model = $app->make(PluginStateModel::class);

        foreach ($app->make(PluginManager::class)->all() as $plugin) {
            if (!($plugin['required'] ?? false)) {
                $model->upsert($plugin['key'], (bool) ($plugin['enabled'] ?? false));
            }
        }
    }

    private function seedAdminUser(Application $app): void
    {
        $users = $app->make(UserModel::class);
        $email = (string) env('ADMIN_SEED_EMAIL', 'admin@example.pl');

        if ($users->findByEmail($email) !== null) {
            return;
        }

        $users->create([
            'name' => (string) env('ADMIN_SEED_NAME', 'Administrator'),
            'email' => $email,
            'password_hash' => password_hash((string) env('ADMIN_SEED_PASSWORD', 'pass'), PASSWORD_DEFAULT),
            'role' => 'superadmin',
        ]);
    }

    private function seedBlogPosts(Application $app): void
    {
        $posts = $app->make(BlogPostModel::class);

        if ($posts->allForAdmin() !== []) {
            return;
        }

        $posts->create([
            'title' => 'Przykładowy wpis blogowy',
            'slug' => 'przykladowy-wpis-blogowy',
            'excerpt' => 'To jest przykładowy wpis blogowy. Zmień go lub usuń w panelu administracyjnym.',
            'content' => '<p>Treść przykładowego wpisu blogowego. Edytuj w panelu admina.</p>',
            'thumbnail_path' => '',
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'seo_title' => 'Przykładowy wpis blogowy',
            'seo_description' => 'Przykładowy wpis blogowy do zastąpienia.',
            'external_url' => '',
        ]);
    }
}
