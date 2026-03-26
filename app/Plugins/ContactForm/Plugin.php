<?php

declare(strict_types=1);

namespace App\Plugins\ContactForm;

use App\Http\Controllers\ContactFormController;
use App\Http\Middleware\VerifyCsrfMiddleware;
use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;

class Plugin implements PluginInterface
{
    public function definition(): array
    {
        return [
            'key' => 'contact-form',
            'name' => 'Formularz kontaktowy',
            'description' => 'Obsługa formularza kontaktowego z walidacją, CSRF i fallbackiem do logów.',
            'required' => true,
            'enabled_by_default' => true,
        ];
    }

    public function register(PluginApi $api): void
    {
        $api->post('/kontakt/wyslij/', [ContactFormController::class, 'submit'], [VerifyCsrfMiddleware::class]);
    }
}
