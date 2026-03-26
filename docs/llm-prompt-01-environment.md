Pracujesz na **Ercoding CMS** uruchamianym lokalnie na **Windows + XAMPP**.

## Kontekst środowiska

Przyjmij te założenia jako domyślne:

- środowisko lokalne: **XAMPP**
- serwer WWW: **Apache z XAMPP**
- baza danych: **MySQL/MariaDB z XAMPP**
- projekt działa z katalogu `C:\xampp\htdocs\...`
- publiczny entrypoint aplikacji jest w `public/`
- backend: **PHP**
- frontend build: **Vite + SCSS + ES modules**

## Twój cel w tym promptcie

Nie budujesz jeszcze nowej strony klienta.

Najpierw masz:

1. zrozumieć architekturę Ercoding CMS,
2. przygotować czyste środowisko startowe,
3. utworzyć lub odtworzyć bazę danych,
4. wykonać pełny reset projektu,
5. usunąć **wszelkie** stare treści i ślady poprzedniego projektu,
6. zostawić tylko działający szkielet CMS z dokumentacją i zasadami do dalszej pracy.

## Najpierw przeczytaj te pliki

W tej kolejności:

1. `docs/llm-instructions.md`
2. `docs/architecture.md`
3. `docs/content-model.md`
4. `docs/how-to-create-page.md`
5. `docs/plugin-api.md`
6. `README.md`
7. `reset.php`

Potem sprawdź:

- `.env`
- `config/database.php`
- `config/pages.php`
- `config/settings.php`
- `config/plugins.php`
- `database/seeders/DatabaseSeeder.php`
- `app/Plugins/` (sprawdź defaults w każdym pluginie)
- `resources/views/` (sprawdź hardcoded treści w szablonach)
- `database/`
- `public/assets/images/`
- `public/uploads/`

## Twarde zasady

- Nie zmieniaj architektury CMS.
- Nie dodawaj frameworków, ORM-ów, page builderów ani zbędnych warstw.
- Nie zostawiaj starych treści klienta, starych maili, telefonów, linków, uploadów i assetów projektu.
- Ma zostać **szkielet CMS**, nie gotowa stara strona.
- Zachowaj działające: panel admina, routing, CMS fields, plugin system, SEO, blog, formularz kontaktowy, user auth, reset workflow.
- Jeśli jakaś grafika lub plik jest tylko pozostałością po poprzednim projekcie i nie należy do szkieletu CMS, usuń go.
- Jeśli jakiś plik w root projektu nie jest używany przez CMS, usuń go.
- Zostaw tylko placeholdery i assety bazowe potrzebne do działania startera.

## WAŻNE — co oznacza „neutralny starter"

`reset.php --confirm` resetuje dane w bazie **do wartości `default` z plików konfiguracyjnych** (`config/pages.php`, `config/settings.php`, pluginy). Samo uruchomienie resetu NIE wystarczy, jeśli te defaulty zawierają stare treści.

**Musisz zneutralizować ŹRÓDŁA defaultów, a nie tylko bazę.** Konkretnie:

1. **`config/pages.php`** — wartości `default` we wszystkich polach muszą być generycznymi placeholderami (np. „Nagłówek strony głównej", „Opis usługi. Zmień w panelu."), NIE treściami marketingowymi czy opisami produktu.
2. **`config/settings.php`** — `company_name` → „Nazwa Firmy", `phone` → „+48 000 000 000", `email` → „kontakt@example.pl", `logo` i `favicon` → `logo-placeholder.svg`, SEO → generyczne placeholdery.
3. **Pluginy (`app/Plugins/*/Plugin.php`)** — sprawdź defaults w każdym pluginie. Jeśli jakiś plugin ma w swoich defaults prawdziwe URL-e, linki do profili społecznościowych, dane kontaktowe — wyczyść je (puste tablice lub generyczne placeholdery).
4. **`reset.php`** — sekcja seed blog posts. Posty muszą być generyczne (np. „Przykładowy wpis blogowy"), nie linkowały do żadnej zewnętrznej strony.
5. **`database/seeders/DatabaseSeeder.php`** — analogicznie jak reset.php.
6. **Szablony (`resources/views/`)** — sprawdź hardcoded treści. Jeśli w szablonie jest wpisana na sztywno nazwa firmy, marka, slogan — zamień na dynamiczną wartość z `$siteSettings` lub usuń.
7. **Assety (`public/assets/images/`)** — pliki brandingowe (logo firmy, favicon z logo) usuń. Zostaw tylko `logo-placeholder.svg` i `og-default.svg`.

**Dopiero po zneutralizowaniu źródeł** uruchom `php reset.php --confirm`, żeby neutralne dane trafiły do bazy.

## Co masz zrobić krok po kroku

1. Potwierdź, że projekt działa w środowisku XAMPP.
2. Sprawdź konfigurację `.env` i połączenie do bazy (w tym czy `APP_URL` pasuje do katalogu projektu).
3. Jeśli baza danych nie istnieje, utwórz ją samodzielnie w MySQL/MariaDB z XAMPP.
4. Jeśli trzeba, uruchom migracje.
5. **Zneutralizuj źródła defaultów** (patrz sekcja „WAŻNE" wyżej).
6. Wykonaj `php reset.php --confirm`.
7. Sprawdź w bazie, że dane są neutralne (brak starych nazw, URL-i, maili, telefonów).
8. Wyczyść `public/uploads/` i projektowe assety, które nie są częścią szkieletu.
9. Usuń branded pliki z `public/assets/images/` (stare logo, stare favicony).
10. Sprawdź szablony pod kątem hardcoded treści i zamień na dynamiczne.
11. Zbuduj frontend (`npm run build`).
12. Potwierdź, że admin i strona publiczna działają.
13. Opisz dokładnie co zostało wyczyszczone i co zostało zachowane.

## Baza danych

Jeśli baza nie istnieje, masz ją stworzyć sam.

Zakładaj środowisko XAMPP i użyj konfiguracji projektu. Jeśli trzeba, wykonaj odpowiednik:

```sql
CREATE DATABASE IF NOT EXISTS nazwa_bazy
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Następnie doprowadź projekt do stanu roboczego zgodnego z jego własnym flow migracji/seed/reset.

## Reset projektu

Masz użyć wbudowanego resetu CMS.

Priorytet:

1. **Najpierw** zneutralizuj źródła defaultów w plikach konfiguracyjnych, pluginach, seederach i szablonach.
2. **Potem** uruchom `php reset.php --confirm`.
3. **Na końcu** zweryfikuj w bazie i na stronie, że nie ma żadnych starych treści.

Reset ma finalnie oznaczać:

- brak starych treści stron (w bazie I w defaultach konfiguracyjnych),
- brak starych wpisów blogowych i wiadomości,
- brak starych uploadów,
- brak starych linków, maili, telefonów, URL-i i danych marki — zarówno w bazie jak i w plikach źródłowych,
- brak starych profili społecznościowych w pluginach,
- brak hardcoded treści brandingowych w szablonach PHP,
- brak branded plików graficznych (logo, favicon) — tylko neutralne placeholdery,
- zostaje tylko działający, neutralny starter bez jakichkolwiek śladów poprzedniego projektu.

## Co ma zostać po wszystkim

Po zakończeniu tego promptu mają zostać:

- działający Ercoding CMS (nazwa CMS-a w panelu admina zostaje — to nazwa narzędzia, nie branding klienta),
- sprawna baza danych z neutralnymi placeholderami,
- działający panel admina,
- sprawne funkcje core,
- generyczne placeholdery we wszystkich treściach i ustawieniach,
- dokumentacja i wytyczne dla kolejnego promptu.

Nie ma zostać:

- stary branding klienta ani poprzedniego projektu,
- stare logotypy, favicony i grafiki projektowe,
- stare telefony, maile, linki, URL-e i profile społecznościowe,
- stare uploady i obrazy w `public/uploads/`,
- stare treści marketingowe w defaultach konfiguracyjnych,
- zbędne pliki w root lub `public`, jeśli nie są używane przez CMS.

## Weryfikacja końcowa

Na końcu sprawdź:

- czy panel admina się otwiera,
- czy baza działa,
- czy reset się wykonał,
- czy frontend się buduje,
- czy żaden plik konfiguracyjny nie zawiera starych treści w defaultach,
- czy szablony nie mają hardcoded brandingu,
- czy `public/assets/images/` zawiera tylko neutralne placeholdery,
- czy starter nie zawiera śmieci po poprzednim projekcie,
- czy można przejść do promptu projektowego.

## Format odpowiedzi

Odpowiedz w 3 krótkich sekcjach:

1. `Environment`
2. `Reset`
3. `Ready For Project Prompt`

W każdej sekcji napisz konkretnie co zrobiłeś i co zostało.
