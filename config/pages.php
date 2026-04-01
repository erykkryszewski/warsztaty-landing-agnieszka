<?php

declare(strict_types=1);

return [
    [
        'key' => 'home',
        'slug' => '/',
        'title' => 'Home',
        'admin_label' => 'Landing page',
        'navigation_label' => 'Start',
        'view' => 'pages/home',
        'show_in_navigation' => false,
        'groups' => [
            [
                'key' => 'hero',
                'label' => 'Hero',
                'fields' => [
                    [
                        'name' => 'page_title',
                        'type' => 'text',
                        'label' => 'Tytul SEO / karta przegladarki',
                        'help' => 'To pole zmienia tytul w karcie przegladarki i SEO. Duzy naglowek na stronie zmienia pole "Naglowek widoczny na stronie".',
                        'default' => 'Kiełkująca Droga | Wyjazd rozwojowo-warsztatowy dla kobiet',
                    ],
                    [
                        'name' => 'eyebrow',
                        'type' => 'text',
                        'label' => 'Eyebrow',
                        'default' => 'Wyjazd rozwojowo-warsztatowy dla kobiet',
                    ],
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Naglowek widoczny na stronie',
                        'help' => 'To jest glowny tekst widoczny w sekcji hero na stronie glownej.',
                        'default' => 'Zatrzymaj się. Posłuchaj siebie.',
                    ],
                    [
                        'name' => 'lead',
                        'type' => 'textarea',
                        'label' => 'Lead',
                        'default' => 'Kiełkująca Droga to 3-dniowy wyjazd dla kobiet, które chcą lepiej rozumieć siebie, swoje potrzeby i wartości — i uczyć się im ufać, odnajdując więcej równowagi w życiu.',
                    ],
                    [
                        'name' => 'microcopy',
                        'type' => 'text',
                        'label' => 'Microcopy',
                        'default' => '',
                    ],
                    [
                        'name' => 'primary_cta_label',
                        'type' => 'text',
                        'label' => 'Etykieta głównego CTA',
                        'default' => 'Zarezerwuj miejsce',
                    ],
                    [
                        'name' => 'primary_cta_url',
                        'type' => 'url',
                        'label' => 'Adres URL głównego CTA',
                        'default' => 'https://forms.gle/41scRza51jVV97Ds6',
                    ],
                    [
                        'name' => 'secondary_cta_label',
                        'type' => 'text',
                        'label' => 'Etykieta pomocniczego CTA',
                        'default' => 'Zobacz, co zawiera wyjazd',
                    ],
                    [
                        'name' => 'secondary_cta_url',
                        'type' => 'url',
                        'label' => 'Adres URL pomocniczego CTA',
                        'default' => '#co-zawiera',
                    ],
                    [
                        'name' => 'image',
                        'type' => 'image',
                        'label' => 'Zdjęcie hero',
                        'default' => '/assets/images/retreat/hero-cottage.jpg',
                    ],
                    [
                        'name' => 'image_alt',
                        'type' => 'text',
                        'label' => 'Opis zdjęcia hero',
                        'default' => 'Kobieta odpoczywająca blisko natury podczas warsztatu',
                    ],
                    [
                        'name' => 'info_chips',
                        'type' => 'repeater',
                        'label' => 'Chipy informacyjne',
                        'button_label' => 'Dodaj chip',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'text',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => '10–12.04.2026'],
                            ['text' => 'Spokojna Dolina, Beskid Wyspowy'],
                            ['text' => 'mała, bezpieczna grupa'],
                            ['text' => 'praca z ciałem, emocjami i naturą'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'audience',
                'label' => 'Czy to dla Ciebie',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Czy czujesz, że...',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Zdania',
                        'button_label' => 'Dodaj zdanie',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => 'Trudno Ci usłyszeć, czego naprawdę potrzebujesz — masz wrażenie, że idziesz za głosem innych, nie za sobą.'],
                            ['text' => 'Nosisz w sobie dużo emocji, ale nie zawsze wiesz, co one oznaczają i jak na nie odpowiedzieć.'],
                            ['text' => 'Trudno Ci odróżnić własne potrzeby od oczekiwań i dawnych doświadczeń.'],
                            ['text' => 'Dużo dajesz innym, a zadbanie o siebie przychodzi Ci z trudem lub poczuciem winy.'],
                            ['text' => 'Chcesz bardziej ufać sobie i swoim decyzjom.'],
                            ['text' => 'Potrzebujesz czasu dla siebie, żeby poukładać to, co dzieje się w Tobie.'],
                        ],
                    ],
                    [
                        'name' => 'closing',
                        'type' => 'textarea',
                        'label' => 'Zamknięcie sekcji',
                        'default' => 'Jeśli choć jedno z tych zdań jest Ci bliskie, ten warsztat może być dla Ciebie dobrym i pomocnym krokiem.',
                    ],
                ],
            ],
            [
                'key' => 'retreat',
                'label' => 'Czym jest wyjazd',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Czym jest ten wyjazd?',
                    ],
                    [
                        'name' => 'lead',
                        'type' => 'textarea',
                        'label' => 'Wprowadzenie',
                        'default' => 'Pracujemy łagodnie i uważnie — w Twoim tempie. To czas, żeby pobyć bliżej siebie, swojego ciała, emocji i tego, co jest w Tobie ważne.',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Elementy sekcji',
                        'button_label' => 'Dodaj element',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => 'Dajesz sobie przestrzeń na bycie ze sobą i z innymi kobietami.'],
                            ['text' => 'Wracasz do kontaktu z ciałem i uczysz się zauważać jego sygnały.'],
                            ['text' => 'Zaczynasz lepiej rozumieć swoje emocje i potrzeby.'],
                            ['text' => 'Uczysz się odróżniać to, co Twoje, od tego, co wynika z wcześniejszych doświadczeń.'],
                            ['text' => 'Przyglądasz się swoim wartościom i temu, co naprawdę jest dla Ciebie ważne.'],
                            ['text' => 'Doświadczasz przestrzeni, w której można być autentyczną i otrzymać wsparcie.'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'pillars',
                'label' => '3 filary',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Dlaczego ten warsztat działa?',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Filary',
                        'button_label' => 'Dodaj filar',
                        'fields' => [
                            [
                                'name' => 'title',
                                'type' => 'text',
                                'label' => 'Tytuł',
                            ],
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Opis',
                            ],
                        ],
                        'default' => [
                            [
                                'title' => 'Leczący wpływ natury',
                                'text' => 'Kontakt z przyrodą pod opieką przewodniczki terapii leśnej wspiera wyciszenie, reguluje układ nerwowy i pomaga wrócić do równowagi.',
                            ],
                            [
                                'title' => 'Krąg kobiet',
                                'text' => 'Bezpieczna, wspierająca grupa daje doświadczenie bycia widzianą, słyszaną i rozumianą. To ważna część procesu zmiany.',
                            ],
                            [
                                'title' => 'Sprawdzone metody',
                                'text' => 'Korzystamy z narzędzi psychoterapii, pracy z ciałem i uważności, opartych na wiedzy o emocjach, relacji i funkcjonowaniu układu nerwowego.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'benefits',
                'label' => 'Co zabierzesz',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Co możesz z tego zabrać dla siebie?',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Korzyści',
                        'button_label' => 'Dodaj korzyść',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => 'Więcej spokoju i lekkości.'],
                            ['text' => 'Lepsze rozumienie własnych emocji i potrzeb.'],
                            ['text' => 'Większą jasność w tym, co jest dla Ciebie ważne.'],
                            ['text' => 'Więcej zaufania do siebie i swoich decyzji.'],
                            ['text' => 'Konkretne narzędzia do codziennej praktyki.'],
                            ['text' => 'Większą umiejętność radzenia sobie z napięciem.'],
                            ['text' => 'Doświadczenie bycia zobaczoną, usłyszaną i przyjętą.'],
                            ['text' => 'Poczucie, że możesz dbać o siebie w sposób bliższy Tobie.'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'methods',
                'label' => 'Jak pracujemy',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Jak pracujemy?',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Elementy programu',
                        'button_label' => 'Dodaj element',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => 'Praca z ciałem i oddechem oraz emocjami i potrzebami.'],
                            ['text' => 'Praktyki mindfulness — zatrzymanie i zauważanie swojego doświadczenia.'],
                            ['text' => 'Uważny kontakt z naturą i kąpiel leśna.'],
                            ['text' => 'Wyciszenie napięć i regulacja układu nerwowego.'],
                            ['text' => 'Przestrzeń ciszy, integracji i bycia ze sobą.'],
                            ['text' => 'Wizualizacje i formy twórcze (rysunek, taniec, symbole).'],
                            ['text' => 'Praca z workbookiem — dla pogłębiania procesu.'],
                            ['text' => 'Elementy terapii dialektyczno-behawioralnej oraz terapii akceptacji i zaangażowania.'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'emotion',
                'label' => 'Blok emocjonalny',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => '',
                    ],
                    [
                        'name' => 'body',
                        'type' => 'textarea',
                        'label' => 'Treść',
                        'default' => '',
                    ],
                    [
                        'name' => 'image',
                        'type' => 'image',
                        'label' => 'Zdjęcie sekcji',
                        'default' => '',
                    ],
                    [
                        'name' => 'image_alt',
                        'type' => 'text',
                        'label' => 'Opis zdjęcia sekcji',
                        'default' => '',
                    ],
                ],
            ],
            [
                'key' => 'video',
                'label' => 'Film',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Zobacz, jak wygląda wyjazd',
                    ],
                    [
                        'name' => 'poster',
                        'type' => 'image',
                        'label' => 'Miniatura / placeholder',
                        'default' => '/assets/images/client/video-placeholder.jpg',
                    ],
                    [
                        'name' => 'poster_alt',
                        'type' => 'text',
                        'label' => 'Opis miniaturki',
                        'default' => 'Podgląd filmu z wyjazdu warsztatowego',
                    ],
                    [
                        'name' => 'video_url',
                        'type' => 'text',
                        'label' => 'Ścieżka do pliku wideo',
                        'default' => '/assets/images/client/video.mp4',
                    ],
                ],
            ],
            [
                'key' => 'team',
                'label' => 'Prowadzące',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Kto Ci w tym towarzyszy?',
                    ],
                    [
                        'name' => 'summary',
                        'type' => 'textarea',
                        'label' => 'Zdanie pod sekcją',
                        'default' => 'Łączymy doświadczenie terapeutyczne z pracą poprzez naturę.',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Prowadzące',
                        'button_label' => 'Dodaj osobę',
                        'fields' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'label' => 'Imię i nazwisko',
                            ],
                            [
                                'name' => 'role',
                                'type' => 'text',
                                'label' => 'Rola',
                            ],
                            [
                                'name' => 'bio',
                                'type' => 'textarea',
                                'label' => 'Opis',
                            ],
                            [
                                'name' => 'photo',
                                'type' => 'image',
                                'label' => 'Zdjęcie',
                            ],
                            [
                                'name' => 'photo_alt',
                                'type' => 'text',
                                'label' => 'Opis zdjęcia',
                            ],
                        ],
                        'default' => [
                            [
                                'name' => 'Agnieszka Doktor',
                                'role' => 'Psychoterapeutka integracyjna, psycholożka i pedagożka',
                                'bio' => 'Pracuje z emocjami, relacją i ciałem. Prowadzi gabinet Przestrzeń Zmiany, wspiera dorosłych, młodzież i rodziców. W swojej pracy łączy uważność, autentyczny kontakt i podejście oparte na relacji.',
                                'photo' => '/assets/images/client/agnieszka.jpg',
                                'photo_alt' => 'Portret Agnieszki Doktor',
                            ],
                            [
                                'name' => 'Patrycja Stukator',
                                'role' => 'Dyplomowana ekoterapeutka i przewodniczka terapii leśnej',
                                'bio' => 'Absolwentka studiów podyplomowych SWPS, certyfikowana przewodniczka kąpieli leśnych i terapii leśnej. Twórczyni projektu Czuję Las i autorka kart „Podróż do wnętrza lasu”. Prowadzi warsztaty i sesje, które pomagają wracać do kontaktu z naturą, zmysłami i ciałem.',
                                'photo' => '/assets/images/client/patrycja.jpg',
                                'photo_alt' => 'Portret Patrycji Stukator',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'proof',
                'label' => 'Social proof',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Zobacz, jak było na poprzednim wyjeździe',
                    ],
                    [
                        'name' => 'intro',
                        'type' => 'textarea',
                        'label' => 'Wprowadzenie',
                        'default' => 'Te zdjęcia pokazują atmosferę wyjazdu: bliskość natury, kobiece wsparcie i spokojne tempo, w którym naprawdę można się zatrzymać, odetchnąć i pobyć bliżej siebie.',
                    ],
                    [
                        'name' => 'gallery_title',
                        'type' => 'text',
                        'label' => 'Tytuł galerii',
                        'default' => 'Klimat wyjazdu',
                    ],
                    [
                        'name' => 'videos_title',
                        'type' => 'text',
                        'label' => 'Tytuł opinii video',
                        'default' => 'Opinie video',
                    ],
                    [
                        'name' => 'quotes_title',
                        'type' => 'text',
                        'label' => 'Tytuł cytatów',
                        'default' => 'Poznaj opinie po naszym wyjeździe',
                    ],
                    [
                        'name' => 'gallery_items',
                        'type' => 'repeater',
                        'label' => 'Zdjęcia galerii',
                        'button_label' => 'Dodaj zdjęcie',
                        'min_items' => 6,
                        'fields' => [
                            [
                                'name' => 'image',
                                'type' => 'image',
                                'label' => 'Zdjęcie',
                            ],
                            [
                                'name' => 'alt',
                                'type' => 'text',
                                'label' => 'Opis zdjęcia',
                            ],
                        ],
                        'default' => [
                            [
                                'image' => '/assets/images/client/portfolio1.jpg',
                                'alt' => 'Chwile z warsztatów — bliskość natury i uważność',
                            ],
                            [
                                'image' => '/assets/images/client/portfolio2.jpg',
                                'alt' => 'Wspólna praca w małej grupie kobiet',
                            ],
                            [
                                'image' => '/assets/images/client/portfolio3.jpg',
                                'alt' => 'Kontakt z naturą i chwila wyciszenia',
                            ],
                            [
                                'image' => '/assets/images/client/portfolio4.jpg',
                                'alt' => 'Przestrzeń warsztatu — spokojna atmosfera',
                            ],
                        ],
                    ],
                    [
                        'name' => 'videos',
                        'type' => 'repeater',
                        'label' => 'Linki do opinii video',
                        'button_label' => 'Dodaj opinię video',
                        'fields' => [
                            [
                                'name' => 'title',
                                'type' => 'text',
                                'label' => 'Tytuł',
                            ],
                            [
                                'name' => 'url',
                                'type' => 'url',
                                'label' => 'Adres URL',
                            ],
                        ],
                        'default' => [],
                    ],
                    [
                        'name' => 'quotes',
                        'type' => 'repeater',
                        'label' => 'Cytaty',
                        'button_label' => 'Dodaj cytat',
                        'fields' => [
                            [
                                'name' => 'quote',
                                'type' => 'textarea',
                                'label' => 'Treść cytatu',
                            ],
                            [
                                'name' => 'author',
                                'type' => 'text',
                                'label' => 'Autor',
                            ],
                        ],
                        'default' => [
                            [
                                'quote' => 'Oczywiście o samą atmosferę na warsztatach, poczucie otulenia, możliwość otworzenia się i szczerej rozmowy zadbało Dzikie Siostrzeństwo – Agnieszka i Patrycja. Warsztaty były bardzo poruszające i dały mi wiele do myślenia. Pracy było dużo, ale w tym wszystkim był również czas na odpoczynek – opuszczałam warsztaty z głową pełną myśli - myśli w końcu lepiej ułożonych, z jednoczesnym poczuciem, że moje ciało jest zrelaksowane!',
                                'author' => 'Aga',
                            ],
                            [
                                'quote' => 'Bardzo doceniłam obecność innych uczestniczek, z którymi mogłam wymienić podobne doświadczenia i tym samym lepiej zrozumieć siebie. Cieszę się, że grupa była kameralna, a kobieca energia pozwoliła mi bezpiecznie poczuć i uwolnić swoje emocje. Dzikie Siostrzeństwo dziękuję za tak wartościowe i inspirujące doświadczenie.',
                                'author' => 'Ania',
                            ],
                            [
                                'quote' => 'Z całego serca polecam cudowne warsztaty prowadzone przez Agnieszkę i Patrycję! Warsztaty zorganizowane były w pięknym miejscu w Jasielówce w Beskidzie Niskim, gdzie klimat magicznego domku połączony z zapachem jesieni i pysznej kuchni właścicieli stworzył bezpieczną przestrzeń do pracy ze swoim wewnętrznym dzieckiem.',
                                'author' => '',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'included',
                'label' => 'Co zawiera',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Co zawiera Kiełkująca Droga?',
                    ],
                    [
                        'name' => 'items',
                        'type' => 'repeater',
                        'label' => 'Elementy oferty',
                        'button_label' => 'Dodaj element',
                        'fields' => [
                            [
                                'name' => 'text',
                                'type' => 'textarea',
                                'label' => 'Treść',
                            ],
                        ],
                        'default' => [
                            ['text' => '3 dni warsztatów, noclegi i wegetariańskie posiłki.'],
                            ['text' => 'Sprawdzone narzędzia i ćwiczenia do codziennej praktyki.'],
                            ['text' => 'Koncert mis, ognisko i kontakt z naturą.'],
                            ['text' => 'Workbook i materiały do dalszej pracy.'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'pricing',
                'label' => 'Cena',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Inwestycja w siebie',
                    ],
                    [
                        'name' => 'price_label',
                        'type' => 'text',
                        'label' => 'Etykieta ceny',
                        'default' => 'Cena',
                    ],
                    [
                        'name' => 'price',
                        'type' => 'text',
                        'label' => 'Cena',
                        'default' => '1890 zł',
                    ],
                    [
                        'name' => 'deposit_label',
                        'type' => 'text',
                        'label' => 'Etykieta zadatku',
                        'default' => 'Zadatek',
                    ],
                    [
                        'name' => 'deposit',
                        'type' => 'text',
                        'label' => 'Zadatek',
                        'default' => '400 zł',
                    ],
                    [
                        'name' => 'payment_label',
                        'type' => 'text',
                        'label' => 'Etykieta formalności',
                        'default' => 'Formalności',
                    ],
                    [
                        'name' => 'payment_note',
                        'type' => 'text',
                        'label' => 'Treść formalności',
                        'default' => 'Wpłata do 5 dni od zapisu',
                    ],
                    [
                        'name' => 'cta_label',
                        'type' => 'text',
                        'label' => 'Etykieta CTA',
                        'default' => 'Zarezerwuj swoje miejsce teraz',
                    ],
                    [
                        'name' => 'cta_url',
                        'type' => 'url',
                        'label' => 'Adres URL CTA',
                        'default' => 'https://forms.gle/41scRza51jVV97Ds6',
                    ],
                    [
                        'name' => 'microcopy',
                        'type' => 'textarea',
                        'label' => 'Microcopy',
                        'default' => 'Miejsca są ograniczone — pracujemy w kameralnej grupie.',
                    ],
                ],
            ],
            [
                'key' => 'reservation',
                'label' => 'Rezerwacja',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Rezerwacja',
                    ],
                    [
                        'name' => 'body',
                        'type' => 'textarea',
                        'label' => 'Treść',
                        'default' => 'Wypełnij formularz zgłoszeniowy. Jeśli masz pytania przed zapisem, napisz do nas mailowo.',
                    ],
                    [
                        'name' => 'button_label',
                        'type' => 'text',
                        'label' => 'Etykieta przycisku',
                        'default' => 'Wypełnij formularz zgłoszeniowy',
                    ],
                    [
                        'name' => 'button_url',
                        'type' => 'url',
                        'label' => 'Adres URL przycisku',
                        'default' => 'https://forms.gle/41scRza51jVV97Ds6',
                    ],
                    [
                        'name' => 'note',
                        'type' => 'textarea',
                        'label' => 'Dopisek',
                        'default' => 'Po wypełnieniu formularza i wpłacie zadatku Twoje miejsce zostaje zarezerwowane.',
                    ],
                ],
            ],
            [
                'key' => 'final',
                'label' => 'Finalne CTA',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'label' => 'Nagłówek sekcji',
                        'default' => 'Może to jest właśnie ten moment',
                    ],
                    [
                        'name' => 'body',
                        'type' => 'textarea',
                        'label' => 'Treść',
                        'default' => 'Nie musisz być gotowa na wszystko. Wystarczy, że jesteś gotowa zrobić pierwszy krok.',
                    ],
                    [
                        'name' => 'button_label',
                        'type' => 'text',
                        'label' => 'Etykieta przycisku',
                        'default' => 'Zarezerwuj miejsce',
                    ],
                    [
                        'name' => 'button_url',
                        'type' => 'url',
                        'label' => 'Adres URL przycisku',
                        'default' => 'https://forms.gle/41scRza51jVV97Ds6',
                    ],
                ],
            ],
        ],
    ],
    [
        'key' => 'privacy',
        'slug' => '/polityka-prywatnosci/',
        'title' => 'Privacy Policy',
        'admin_label' => 'Polityka prywatności',
        'navigation_label' => 'Polityka prywatności',
        'view' => 'pages/privacy',
        'show_in_navigation' => false,
        'groups' => [
            [
                'key' => 'main',
                'label' => 'Treść strony',
                'fields' => [
                    [
                        'name' => 'page_title',
                        'type' => 'text',
                        'label' => 'Tytul strony i SEO',
                        'help' => 'To pole ustawia naglowek strony oraz tytul w karcie przegladarki.',
                        'default' => 'Polityka prywatności',
                    ],
                    [
                        'name' => 'body',
                        'type' => 'richtext',
                        'label' => 'Treść strony',
                        'default' => '<h2>1. Administrator danych osobowych</h2>
<p>Operatorem serwisu oraz Administratorem danych osobowych jest: <strong>Agnieszka Doktor – Przestrzeń Zmiany</strong>, ul. Stanisława Moniuszki 20/205, 41-902 Bytom. Kontakt: <a href="mailto:dzikiesiostrzenstwo@gmail.com">dzikiesiostrzenstwo@gmail.com</a>.</p>

<h2>2. Zakres zbieranych danych</h2>
<p>Serwis zbiera dane przekazywane dobrowolnie przez użytkowników za pośrednictwem formularza zgłoszeniowego (imię, nazwisko, adres e-mail, numer telefonu) oraz dane gromadzone automatycznie podczas korzystania z serwisu (adres IP, typ przeglądarki, czas wizyty, odwiedzane podstrony).</p>

<h2>3. Cel przetwarzania danych</h2>
<p>Dane osobowe przetwarzane są w celu: realizacji zgłoszeń na warsztaty i wyjazdy, odpowiadania na zapytania przesłane drogą mailową, prowadzenia analityki ruchu na stronie (Google Analytics), zapewnienia prawidłowego funkcjonowania serwisu oraz realizacji obowiązków prawnych ciążących na Administratorze.</p>

<h2>4. Podstawy prawne przetwarzania</h2>
<p>Dane przetwarzane są na podstawie: zgody osoby, której dane dotyczą (art. 6 ust. 1 lit. a RODO), niezbędności do wykonania umowy lub podjęcia działań przed jej zawarciem (art. 6 ust. 1 lit. b RODO) oraz prawnie uzasadnionego interesu Administratora, w tym prowadzenia analityki i marketingu własnych usług (art. 6 ust. 1 lit. f RODO).</p>

<h2>5. Pliki cookies</h2>
<p>Serwis korzysta z plików cookies (ciasteczek) — niewielkich plików tekstowych zapisywanych na urządzeniu użytkownika. Cookies wykorzystywane są do: prawidłowego działania strony, zapamiętywania preferencji użytkownika, prowadzenia statystyk odwiedzin (Google Analytics) oraz wyświetlania spersonalizowanych treści.</p>
<p>Użytkownik może w każdej chwili zmienić ustawienia dotyczące plików cookies w swojej przeglądarce internetowej, w tym zablokować ich zapisywanie. Może to jednak wpłynąć na poprawne działanie niektórych funkcji serwisu.</p>

<h2>6. Google Analytics</h2>
<p>Serwis korzysta z narzędzia Google Analytics dostarczanego przez Google LLC. Google Analytics używa plików cookies do analizy sposobu korzystania z serwisu. Informacje generowane przez cookies są zazwyczaj przesyłane na serwery Google. Dane te są anonimizowane (maskowanie IP). Więcej informacji o zasadach przetwarzania danych przez Google można znaleźć w polityce prywatności Google.</p>

<h2>7. Okres przechowywania danych</h2>
<p>Dane osobowe przechowywane są przez okres niezbędny do realizacji celów, w jakich zostały zebrane, a po tym czasie — przez okres wymagany przepisami prawa lub do momentu cofnięcia zgody przez osobę, której dane dotyczą.</p>

<h2>8. Prawa użytkownika</h2>
<p>Każda osoba, której dane dotyczą, ma prawo do: dostępu do swoich danych osobowych, ich sprostowania, usunięcia lub ograniczenia przetwarzania, wniesienia sprzeciwu wobec przetwarzania, przenoszenia danych, cofnięcia zgody w dowolnym momencie (bez wpływu na zgodność z prawem przetwarzania dokonanego przed cofnięciem) oraz wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych.</p>

<h2>9. Udostępnianie danych</h2>
<p>Dane osobowe mogą być udostępniane podmiotom wspierającym Administratora w prowadzeniu serwisu i realizacji usług, w tym dostawcom usług hostingowych, narzędzi analitycznych (Google) oraz usług pocztowych. Dane nie są przekazywane do państw trzecich poza przypadkami wynikającymi z korzystania z usług Google.</p>

<h2>10. Zmiany polityki prywatności</h2>
<p>Administrator zastrzega sobie prawo do wprowadzania zmian w niniejszej polityce prywatności. O wszelkich zmianach użytkownicy zostaną poinformowani poprzez zamieszczenie zaktualizowanej wersji na tej stronie.</p>

<p><em>Ostatnia aktualizacja: marzec 2026</em></p>',
                    ],
                ],
            ],
        ],
    ],
];
