<?php

declare(strict_types=1);

namespace App\Plugins\SocialLinks;

use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;

class Plugin implements PluginInterface
{
    public function definition(): array
    {
        return [
            'key' => 'social-links',
            'name' => 'Media społecznościowe',
            'description' => 'Sekcja ustawień strony dla profili społecznościowych firmy.',
            'required' => true,
            'enabled_by_default' => true,
        ];
    }

    public function register(PluginApi $api): void
    {
        $api->settingsSection([
            'key' => 'social_links',
            'label' => 'Media społecznościowe',
            'fields' => [
                [
                    'name' => 'links',
                    'type' => 'repeater',
                    'label' => 'Profile',
                    'button_label' => 'Dodaj profil',
                    'default' => [],
                    'fields' => [
                        [
                            'name' => 'icon',
                            'type' => 'text',
                            'label' => 'Ikona lub nazwa',
                        ],
                        [
                            'name' => 'label',
                            'type' => 'text',
                            'label' => 'Etykieta',
                        ],
                        [
                            'name' => 'url',
                            'type' => 'url',
                            'label' => 'Adres URL',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
