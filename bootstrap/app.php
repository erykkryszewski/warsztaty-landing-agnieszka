<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Env;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Deployment\DeployPackageManager;
use App\Models\BlogPostModel;
use App\Models\ContactMessageModel;
use App\Models\PageModel;
use App\Models\PluginStateModel;
use App\Models\SettingModel;
use App\Models\UserModel;
use App\Services\AuthService;
use App\Services\ContentFieldService;
use App\Services\ContactFormService;
use App\Services\MailService;
use App\Services\PageRegistry;
use App\Services\PageService;
use App\Services\PluginManager;
use App\Services\PostService;
use App\Services\RateLimiterService;
use App\Services\SeoService;
use App\Services\SettingsRegistry;
use App\Services\SettingsService;
use App\Services\UploadService;
use App\Services\UserService;

$app = new Application(BASE_PATH);

Env::load($app->basePath('.env'));

$app->loadConfig([
    'app' => $app->basePath('config/app.php'),
    'database' => $app->basePath('config/database.php'),
    'mail' => $app->basePath('config/mail.php'),
    'pages' => $app->basePath('config/pages.php'),
    'settings' => $app->basePath('config/settings.php'),
    'plugins' => $app->basePath('config/plugins.php'),
]);

date_default_timezone_set((string) $app->config('app.timezone', 'Europe/Warsaw'));

$appUrl = parse_url((string) $app->config('app.url', ''));
$isHttps = (($appUrl['scheme'] ?? '') === 'https')
    || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) === '443');

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('ercms_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

$app->singleton(Session::class, static fn (): Session => new Session());
$app->singleton(Csrf::class, static fn (Application $app): Csrf => new Csrf($app->make(Session::class)));
$app->singleton(Database::class, static fn (Application $app): Database => new Database($app->config('database')));
$app->singleton(View::class, static fn (Application $app): View => new View($app));
$app->singleton(Router::class, static fn (Application $app): Router => new Router($app));
$app->singleton(DeployPackageManager::class, static fn (Application $app): DeployPackageManager => new DeployPackageManager($app->basePath()));

$app->singleton(ContentFieldService::class, static fn (Application $app): ContentFieldService => new ContentFieldService($app));
$app->singleton(UploadService::class, static fn (Application $app): UploadService => new UploadService($app));
$app->singleton(RateLimiterService::class, static fn (Application $app): RateLimiterService => new RateLimiterService($app));

$app->singleton(SettingModel::class, static fn (Application $app): SettingModel => new SettingModel($app->make(Database::class)));
$app->singleton(PageModel::class, static fn (Application $app): PageModel => new PageModel($app->make(Database::class)));
$app->singleton(UserModel::class, static fn (Application $app): UserModel => new UserModel($app->make(Database::class)));
$app->singleton(BlogPostModel::class, static fn (Application $app): BlogPostModel => new BlogPostModel($app->make(Database::class)));
$app->singleton(ContactMessageModel::class, static fn (Application $app): ContactMessageModel => new ContactMessageModel($app->make(Database::class)));
$app->singleton(PluginStateModel::class, static fn (Application $app): PluginStateModel => new PluginStateModel($app->make(Database::class)));

$app->singleton(SettingsRegistry::class, static fn (Application $app): SettingsRegistry => new SettingsRegistry($app));
$app->singleton(PageRegistry::class, static fn (Application $app): PageRegistry => new PageRegistry($app));
$app->singleton(PluginManager::class, static fn (Application $app): PluginManager => new PluginManager($app));

$app->singleton(SettingsService::class, static fn (Application $app): SettingsService => new SettingsService(
    $app->make(SettingModel::class),
    $app->make(SettingsRegistry::class),
    $app->make(ContentFieldService::class),
    $app->make(UploadService::class)
));

$app->singleton(PageService::class, static fn (Application $app): PageService => new PageService(
    $app->make(PageModel::class),
    $app->make(PageRegistry::class),
    $app->make(ContentFieldService::class),
    $app->make(UploadService::class)
));

$app->singleton(AuthService::class, static fn (Application $app): AuthService => new AuthService(
    $app->make(UserModel::class),
    $app->make(Session::class)
));

$app->singleton(MailService::class, static fn (Application $app): MailService => new MailService($app));
$app->singleton(ContactFormService::class, static fn (Application $app): ContactFormService => new ContactFormService(
    $app->make(ContactMessageModel::class),
    $app->make(MailService::class),
    $app->make(RateLimiterService::class)
));

$app->singleton(PostService::class, static fn (Application $app): PostService => new PostService(
    $app->make(BlogPostModel::class),
    $app->make(UploadService::class)
));

$app->singleton(UserService::class, static fn (Application $app): UserService => new UserService($app->make(UserModel::class)));
$app->singleton(SeoService::class, static fn (Application $app): SeoService => new SeoService($app));

require $app->basePath('routes/web.php');
require $app->basePath('routes/admin.php');

$app->make(PluginManager::class)->boot();

return $app;
