<?php

declare(strict_types=1);

namespace App\Plugins\Testimonials;

use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;

class Plugin implements PluginInterface
{
    public function definition(): array
    {
        return [
            'key' => 'testimonials',
            'name' => 'Opinie klientów',
            'description' => 'Opcjonalna sekcja opinii jako rozszerzenie strony głównej.',
            'required' => false,
            'enabled_by_default' => false,
        ];
    }

    public function register(PluginApi $api): void
    {
        $api->extendPage('home', [
            'key' => 'testimonials',
            'label' => 'Opinie klientów',
            'fields' => [
                [
                    'name' => 'section_title',
                    'type' => 'text',
                    'label' => 'Tytuł sekcji',
                    'default' => 'Klienci wracają do nas po kolejne etapy współpracy',
                ],
                [
                    'name' => 'items',
                    'type' => 'repeater',
                    'label' => 'Opinie',
                    'button_label' => 'Dodaj opinię',
                    'default' => [
                        [
                            'quote' => 'Współpraca była prosta, terminowa i bez zbędnych niespodzianek.',
                            'author' => 'Anna i Michał',
                            'role' => 'Klienci prywatni',
                        ],
                        [
                            'quote' => 'Dostaliśmy czytelny plan i estetyczny efekt przy ograniczonym budżecie.',
                            'author' => 'Biuro Nurt',
                            'role' => 'Klient firmowy',
                        ],
                    ],
                    'fields' => [
                        [
                            'name' => 'quote',
                            'type' => 'textarea',
                            'label' => 'Treść opinii',
                        ],
                        [
                            'name' => 'author',
                            'type' => 'text',
                            'label' => 'Autor',
                        ],
                        [
                            'name' => 'role',
                            'type' => 'text',
                            'label' => 'Podpis',
                        ],
                    ],
                ],
            ],
        ], 'after-intro', 'plugins/testimonials/section');
    }
}
