Pracujesz na świeżo przygotowanym starterze **Ercoding CMS**. Środowisko XAMPP, baza danych i reset projektu zostały już wykonane. Teraz masz zbudować stronę klienta od zera do końca zgodnie z architekturą CMS.

## Najpierw przeczytaj te pliki

W tej kolejności:

1. `docs/llm-instructions.md`
2. `docs/content-model.md`
3. `docs/how-to-create-page.md`
4. `docs/architecture.md`
5. `docs/plugin-api.md` tylko jeśli ruszasz pluginy lub sloty

Potem sprawdź aktualny stan implementacji w:

- `config/pages.php`
- `config/settings.php`
- `config/plugins.php`
- `resources/views/layout/app.php`
- `resources/views/admin/partials/field.php`
- `resources/views/pages/`
- `resources/scss/`
- `resources/js/`
- `app/Plugins/`

## Twój cel

Masz zakodować kompletną stronę lub landing tak, żeby:

- była w pełni edytowalna z poziomu panelu,
- była bezpieczna i lekka,
- miała prosty, przewidywalny kod,
- korzystała z istniejącego modelu CMS,
- nadawała się do dalszego utrzymania przez juniora bez chaosu.

## Zasady kodowania

Trzymaj się ich bez wyjątku:

- używaj **BEM**
- używaj **ES6 modules**
- pisz **opisowe nazwy zmiennych**
- **nie dodawaj komentarzy do kodu**, chyba że bez tego kod staje się nieczytelny
- kod ma być **junior-friendly wizualnie**, ale technicznie mocny
- rozwiązania mają być **proste, bezpieczne i zoptymalizowane**
- nie dokładaj 100000 fallbacków dla wyimaginowanych scenariuszy
- nie overengineeruj
- nie zmieniaj architektury CMS
- nie hardcoduj finalnych treści klienta, jeśli mają być edytowalne

## Jak masz myśleć o CMS

- Struktura strony jest definiowana w kodzie.
- Treści są przechowywane w CMS.
- Klient po wdrożeniu ma edytować treści z panelu, a nie przez kod.
- Każdy tekst, link, obraz, sekcja, karta, lista, CTA i element globalny, który ma być zmienialny, musi być podpięty do CMS.

## Modelowanie treści

Stosuj tylko wspierany model:

- pola pojedyncze: `text`, `textarea`, `richtext`, `image`, `url`, `email`, `phone`
- listy: `repeater`
- dane stron: `config/pages.php`
- dane globalne: `config/settings.php`
- rozszerzenia i sekcje wielokrotnego użytku: pluginy

Nie twórz bocznych struktur danych poza page/settings/plugin groups.

## Jak spinać dane z widokami

Rób to w ten sposób:

1. Definiujesz pola w `config/pages.php` albo `config/settings.php`.
2. Pobierasz je w widoku przez `page_group($page, 'group_key')` albo `$siteSettings[...]`.
3. Dla linków z CMS używasz `content_link(...)`.
4. Dla list używasz `repeater`.
5. Dla pluginów używasz `plugin_slot(...)`, jeśli to potrzebne.

## Frontend rules

- HTML ma być semantyczny.
- CSS ma być lekki.
- JS tylko jeśli realnie potrzebny.
- Używaj istniejącej struktury SCSS.
- Używaj zmiennych motywu:
  - `var(--color-brand)`
  - `var(--color-accent)`
  - `var(--color-text)`
  - `var(--font-body)`
  - `var(--font-heading)`
- Nie hardcoduj kolorów i fontów klienta w SCSS.

## Security / quality rules

- Nie psuj istniejących zabezpieczeń CMS.
- Nie obchodź walidacji.
- Nie wprowadzaj niebezpiecznego HTML/JS do richtextów.
- Nie dodawaj zbędnych zależności.
- Jeśli da się zrobić prościej i bezpieczniej, wybierz prostsze rozwiązanie.

## Workflow implementacyjny

1. Przeczytaj materiały projektowe.
2. Rozpisz strukturę strony.
3. Zdecyduj, co jest globalne, co jest stronowe, a co repeatowalne.
4. Dodaj pola do CMS.
5. Podepnij widoki do tych pól.
6. Dopisz style.
7. Dopisz minimalny JS, jeśli potrzebny.
8. Zrób końcową kontrolę edytowalności.

## Definition of done

Praca jest skończona dopiero wtedy, gdy:

- klient może edytować treści z panelu,
- nie ma hardcode’ów tam, gdzie treść ma być edytowalna,
- kod jest prosty i czytelny,
- strona jest bezpieczna i lekka,
- layout działa na mobile i desktopie,
- po zmianach przechodzą:
  - `php -l` dla zmienionych plików PHP
  - `npm run build`

## Format odpowiedzi

Najpierw krótko opisz:

- strukturę strony,
- pola CMS, które dodasz lub zmienisz,
- pliki, które ruszysz,
- czy potrzebne są pluginy.

Potem przejdź do implementacji.

## STRUKTURA I TREŚCI PROJEKTU

Tutaj wklejam komplet materiałów wejściowych.

### Opis projektu

[Wklej opis firmy, oferty, grupy docelowej, celu strony]

### Mapa strony / sekcje

[Wklej listę podstron albo sekcji]

### Copy

[Wklej teksty od copywritera]

### CTA

[Wklej przyciski, linki, maile, telefony, formularze]

### SEO

[Wklej title, description, frazy, założenia SEO]

### Branding

[Wklej kolory, fonty, styl komunikacji, inspiracje]

### Materiały graficzne

[Wklej listę obrazków, logo, favicon, OG image, zdjęcia, mockupy]

### Wymagania dodatkowe

[Wklej uwagi, integracje, ograniczenia, elementy specjalne]

## Ostatnia instrukcja

Jeśli jakikolwiek element ma być edytowalny po wdrożeniu, musi być podpięty do CMS. Priorytet: prostota, bezpieczeństwo, przewidywalność i pełna edytowalność.
