<?php

declare(strict_types=1);

return [
    [
        'key' => 'business',
        'label' => 'Dane marki',
        'fields' => [
            [
                'name' => 'company_name',
                'type' => 'text',
                'label' => 'Nazwa marki',
                'required' => true,
                'default' => 'Przestrzeń Zmiany',
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'Adres e-mail',
                'required' => true,
                'default' => 'dzikiesiostrzenstwo@gmail.com',
            ],
            [
                'name' => 'phone',
                'type' => 'phone',
                'label' => 'Numer telefonu',
                'default' => '',
            ],
            [
                'name' => 'logo',
                'type' => 'image',
                'label' => 'Logo',
                'help' => 'Opcjonalne. Jeśli pole zostanie puste, w nagłówku pokaże się nazwa marki.',
                'default' => '/assets/images/logo-przestrzen-zmiany.png',
            ],
            [
                'name' => 'favicon',
                'type' => 'image',
                'label' => 'Favicon',
                'help' => 'Obsługiwane formaty: PNG, WebP, SVG, ICO.',
                'default' => '/assets/images/logo-przestrzen-zmiany.png',
            ],
        ],
    ],
    [
        'key' => 'theme',
        'label' => 'Motyw strony',
        'fields' => [
            [
                'name' => 'font_primary',
                'type' => 'text',
                'label' => 'Font główny (body)',
                'help' => 'Nazwa fontu z Google Fonts, np. Manrope, Inter, Open Sans.',
                'default' => 'Manrope',
            ],
            [
                'name' => 'font_decorated',
                'type' => 'text',
                'label' => 'Font nagłówków',
                'help' => 'Nazwa fontu z Google Fonts, np. DM Serif Display, Cormorant Garamond, Fraunces.',
                'default' => 'Cormorant Garamond',
            ],
            [
                'name' => 'color_main',
                'type' => 'text',
                'label' => 'Kolor główny (brand)',
                'help' => 'Kolor HEX używany jako bazowy kolor marki.',
                'default' => '#315740',
            ],
            [
                'name' => 'color_accent',
                'type' => 'text',
                'label' => 'Kolor akcentowy',
                'help' => 'Kolor HEX używany w CTA i wyróżnieniach.',
                'default' => '#cf8f73',
            ],
            [
                'name' => 'color_text',
                'type' => 'text',
                'label' => 'Kolor tekstu',
                'help' => 'Kolor HEX dla głównego tekstu strony.',
                'default' => '#1f2c25',
            ],
        ],
    ],
    [
        'key' => 'seo_defaults',
        'label' => 'SEO domyślne',
        'fields' => [
            [
                'name' => 'default_title',
                'type' => 'text',
                'label' => 'Domyślny tytuł SEO',
                'default' => 'Kiełkująca Droga | Warsztat dla kobiet',
            ],
            [
                'name' => 'default_description',
                'type' => 'textarea',
                'label' => 'Domyślny opis SEO',
                'default' => 'Kiełkująca Droga to 3-dniowy wyjazd rozwojowo-warsztatowy dla kobiet w Beskidzie Wyspowym.',
            ],
            [
                'name' => 'default_og_image',
                'type' => 'image',
                'label' => 'Domyślny obraz OG',
                'default' => '/assets/images/og-default.svg',
            ],
        ],
    ],
];
