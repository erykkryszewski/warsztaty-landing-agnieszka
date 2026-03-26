<?php

declare(strict_types=1);

namespace App\Plugins\Faq;

use App\Plugins\PluginApi;
use App\Plugins\PluginInterface;

class Plugin implements PluginInterface
{
    public function definition(): array
    {
        return [
            'key' => 'faq',
            'name' => 'FAQ',
            'description' => 'Opcjonalna sekcja najczęstszych pytań na stronie usług.',
            'required' => false,
            'enabled_by_default' => true,
        ];
    }

    public function register(PluginApi $api): void
    {
        $api->extendPage('services', [
            'key' => 'faq',
            'label' => 'FAQ',
            'fields' => [
                [
                    'name' => 'section_title',
                    'type' => 'text',
                    'label' => 'Tytuł sekcji',
                    'default' => 'Najczęstsze pytania przed rozpoczęciem współpracy',
                ],
                [
                    'name' => 'items',
                    'type' => 'repeater',
                    'label' => 'Pytania i odpowiedzi',
                    'button_label' => 'Dodaj pytanie',
                    'default' => [
                        [
                            'question' => 'Jak wygląda pierwszy etap współpracy?',
                            'answer' => 'Zaczynamy od rozmowy, krótkiego audytu miejsca i ustalenia priorytetów.',
                        ],
                        [
                            'question' => 'Czy realizujecie małe zakresy prac?',
                            'answer' => 'Tak, starter zakłada obsługę prostych stron i prostych ofert. Tę samą zasadę pokazuje demo usługi.',
                        ],
                    ],
                    'fields' => [
                        [
                            'name' => 'question',
                            'type' => 'text',
                            'label' => 'Pytanie',
                        ],
                        [
                            'name' => 'answer',
                            'type' => 'textarea',
                            'label' => 'Odpowiedź',
                        ],
                    ],
                ],
            ],
        ], 'after-main', 'plugins/faq/section');
    }
}
