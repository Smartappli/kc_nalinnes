<?php
declare(strict_types=1);

function kc_supported_locales(): array {
    return ['fr', 'en', 'nl'];
}

function kc_normalize_locale(?string $locale): string {
    $locale = strtolower(trim((string)$locale));
    $locale = str_replace('_', '-', $locale);
    $locale = explode('-', $locale, 2)[0];

    return in_array($locale, kc_supported_locales(), true) ? $locale : 'fr';
}

function kc_current_locale(): string {
    static $locale = null;

    if ($locale !== null) {
        return $locale;
    }

    if (isset($_GET['lang'])) {
        $locale = kc_normalize_locale((string)$_GET['lang']);
        setcookie('kc_locale', $locale, [
            'expires' => time() + 31536000,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);

        return $locale;
    }

    if (isset($_COOKIE['kc_locale'])) {
        $locale = kc_normalize_locale((string)$_COOKIE['kc_locale']);
        return $locale;
    }

    $locale = kc_normalize_locale($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null);
    return $locale;
}

function kc_translations(): array {
    static $translations = null;

    if ($translations !== null) {
        return $translations;
    }

    $translations = [
        'fr' => [
            'common.brand' => 'KC Nalinnes',
            'common.nav.schedule' => 'Horaires',
            'common.nav.prices' => 'Tarifs',
            'common.nav.calendar' => 'Calendrier',
            'common.nav.instructors' => 'Instructeurs',
            'common.nav.documents' => 'Documents',
            'common.nav.news' => 'Actus',
            'common.nav.contact' => 'Contact',
            'common.nav.members' => 'Membres',
            'common.theme.toggle' => 'Basculer le thème',
            'common.menu.open' => 'Ouvrir le menu',
            'common.footer.rights' => 'Tous droits réservés',
            'common.footer.developed_by' => 'Développé par',
            'common.footer.legal' => 'Mentions légales',
            'common.footer.privacy' => 'Politique de confidentialité',
            'common.language.label' => 'Langue',
            'common.language.fr' => 'FR',
            'common.language.en' => 'EN',
            'common.language.nl' => 'NL',

            'meal.meta.title' => 'Réservation repas de fin de saison - KC Nalinnes',
            'meal.meta.description' => 'Réservation publique pour le repas de fin de saison du KC Nalinnes.',
            'meal.flash.invalid_request' => 'Requête invalide. Veuillez réessayer.',
            'meal.flash.invalid_name' => 'Veuillez indiquer un nom valide.',
            'meal.flash.invalid_email' => 'Veuillez indiquer une adresse email valide.',
            'meal.flash.no_meal' => 'Veuillez sélectionner au moins un repas.',
            'meal.flash.success' => 'Votre réservation repas a bien été enregistrée.',
            'meal.flash.error' => 'Impossible d’enregistrer la réservation pour le moment. Veuillez contacter le club.',
            'meal.mail.admin_subject' => 'Nouvelle réservation repas publique',
            'meal.mail.copy_subject' => 'Copie de votre réservation repas',
            'meal.mail.heading' => 'Réservation publique',
            'meal.mail.reservation_id' => 'Réservation ID',
            'meal.mail.name' => 'Nom',
            'meal.mail.phone' => 'Téléphone',
            'meal.mail.adults' => 'Adultes',
            'meal.mail.children' => 'Enfants',
            'meal.mail.total' => 'Total',
            'meal.mail.notes' => 'Notes',
            'meal.hero.kicker' => 'Fin de saison',
            'meal.hero.title' => 'Réservation repas',
            'meal.hero.description' => 'Réservez le repas de fin de saison du 26 juin 2026 à 20h. Les réservations sont ouvertes aux membres, familles et proches.',
            'meal.card.adult.title' => 'Adulte - 19 EUR',
            'meal.card.adult.description' => '1 brochette + 1 saucisse.',
            'meal.card.child.title' => 'Enfant - 10 EUR',
            'meal.card.child.description' => '1 saucisse ou 1 brochette.',
            'meal.form.name' => 'Nom et prénom',
            'meal.form.email' => 'Email',
            'meal.form.phone' => 'Téléphone',
            'meal.form.adults' => 'Repas adultes',
            'meal.form.children' => 'Repas enfants',
            'meal.form.notes' => 'Remarque éventuelle',
            'meal.form.copy' => 'Recevoir une copie par email',
            'meal.form.submit' => 'Envoyer la réservation',
        ],
        'en' => [
            'common.brand' => 'KC Nalinnes',
            'common.nav.schedule' => 'Schedule',
            'common.nav.prices' => 'Prices',
            'common.nav.calendar' => 'Calendar',
            'common.nav.instructors' => 'Instructors',
            'common.nav.documents' => 'Documents',
            'common.nav.news' => 'News',
            'common.nav.contact' => 'Contact',
            'common.nav.members' => 'Members',
            'common.theme.toggle' => 'Toggle theme',
            'common.menu.open' => 'Open menu',
            'common.footer.rights' => 'All rights reserved',
            'common.footer.developed_by' => 'Developed by',
            'common.footer.legal' => 'Legal notice',
            'common.footer.privacy' => 'Privacy policy',
            'common.language.label' => 'Language',
            'common.language.fr' => 'FR',
            'common.language.en' => 'EN',
            'common.language.nl' => 'NL',

            'meal.meta.title' => 'End-of-season meal reservation - KC Nalinnes',
            'meal.meta.description' => 'Public reservation form for the KC Nalinnes end-of-season meal.',
            'meal.flash.invalid_request' => 'Invalid request. Please try again.',
            'meal.flash.invalid_name' => 'Please enter a valid name.',
            'meal.flash.invalid_email' => 'Please enter a valid email address.',
            'meal.flash.no_meal' => 'Please select at least one meal.',
            'meal.flash.success' => 'Your meal reservation has been saved.',
            'meal.flash.error' => 'Unable to save the reservation right now. Please contact the club.',
            'meal.mail.admin_subject' => 'New public meal reservation',
            'meal.mail.copy_subject' => 'Copy of your meal reservation',
            'meal.mail.heading' => 'Public reservation',
            'meal.mail.reservation_id' => 'Reservation ID',
            'meal.mail.name' => 'Name',
            'meal.mail.phone' => 'Phone',
            'meal.mail.adults' => 'Adults',
            'meal.mail.children' => 'Children',
            'meal.mail.total' => 'Total',
            'meal.mail.notes' => 'Notes',
            'meal.hero.kicker' => 'End of season',
            'meal.hero.title' => 'Meal reservation',
            'meal.hero.description' => 'Book the end-of-season meal on June 26, 2026 at 8:00 PM. Reservations are open to members, families and friends.',
            'meal.card.adult.title' => 'Adult - EUR 19',
            'meal.card.adult.description' => '1 skewer + 1 sausage.',
            'meal.card.child.title' => 'Child - EUR 10',
            'meal.card.child.description' => '1 sausage or 1 skewer.',
            'meal.form.name' => 'First and last name',
            'meal.form.email' => 'Email',
            'meal.form.phone' => 'Phone',
            'meal.form.adults' => 'Adult meals',
            'meal.form.children' => 'Child meals',
            'meal.form.notes' => 'Optional note',
            'meal.form.copy' => 'Receive a copy by email',
            'meal.form.submit' => 'Send reservation',
        ],
        'nl' => [
            'common.brand' => 'KC Nalinnes',
            'common.nav.schedule' => 'Uurrooster',
            'common.nav.prices' => 'Tarieven',
            'common.nav.calendar' => 'Kalender',
            'common.nav.instructors' => 'Instructeurs',
            'common.nav.documents' => 'Documenten',
            'common.nav.news' => 'Nieuws',
            'common.nav.contact' => 'Contact',
            'common.nav.members' => 'Leden',
            'common.theme.toggle' => 'Thema wijzigen',
            'common.menu.open' => 'Menu openen',
            'common.footer.rights' => 'Alle rechten voorbehouden',
            'common.footer.developed_by' => 'Ontwikkeld door',
            'common.footer.legal' => 'Wettelijke vermeldingen',
            'common.footer.privacy' => 'Privacybeleid',
            'common.language.label' => 'Taal',
            'common.language.fr' => 'FR',
            'common.language.en' => 'EN',
            'common.language.nl' => 'NL',

            'meal.meta.title' => 'Reservatie einde-seizoensmaaltijd - KC Nalinnes',
            'meal.meta.description' => 'Publiek reservatieformulier voor de einde-seizoensmaaltijd van KC Nalinnes.',
            'meal.flash.invalid_request' => 'Ongeldige aanvraag. Probeer opnieuw.',
            'meal.flash.invalid_name' => 'Vul een geldige naam in.',
            'meal.flash.invalid_email' => 'Vul een geldig e-mailadres in.',
            'meal.flash.no_meal' => 'Selecteer minstens een maaltijd.',
            'meal.flash.success' => 'Je maaltijdreservatie werd opgeslagen.',
            'meal.flash.error' => 'De reservatie kan momenteel niet worden opgeslagen. Neem contact op met de club.',
            'meal.mail.admin_subject' => 'Nieuwe publieke maaltijdreservatie',
            'meal.mail.copy_subject' => 'Kopie van je maaltijdreservatie',
            'meal.mail.heading' => 'Publieke reservatie',
            'meal.mail.reservation_id' => 'Reservatie ID',
            'meal.mail.name' => 'Naam',
            'meal.mail.phone' => 'Telefoon',
            'meal.mail.adults' => 'Volwassenen',
            'meal.mail.children' => 'Kinderen',
            'meal.mail.total' => 'Totaal',
            'meal.mail.notes' => 'Opmerkingen',
            'meal.hero.kicker' => 'Einde seizoen',
            'meal.hero.title' => 'Maaltijdreservatie',
            'meal.hero.description' => 'Reserveer de einde-seizoensmaaltijd op 26 juni 2026 om 20.00 uur. Reservaties staan open voor leden, families en vrienden.',
            'meal.card.adult.title' => 'Volwassene - EUR 19',
            'meal.card.adult.description' => '1 brochette + 1 worst.',
            'meal.card.child.title' => 'Kind - EUR 10',
            'meal.card.child.description' => '1 worst of 1 brochette.',
            'meal.form.name' => 'Voor- en achternaam',
            'meal.form.email' => 'E-mail',
            'meal.form.phone' => 'Telefoon',
            'meal.form.adults' => 'Maaltijden volwassenen',
            'meal.form.children' => 'Maaltijden kinderen',
            'meal.form.notes' => 'Eventuele opmerking',
            'meal.form.copy' => 'Een kopie per e-mail ontvangen',
            'meal.form.submit' => 'Reservatie verzenden',
        ],
    ];

    return $translations;
}

function kc_t(string $key, array $replace = [], ?string $locale = null): string {
    $locale = kc_normalize_locale($locale ?? kc_current_locale());
    $translations = kc_translations();
    $value = $translations[$locale][$key] ?? $translations['fr'][$key] ?? $key;

    foreach ($replace as $name => $replacement) {
        $value = str_replace('{' . $name . '}', (string)$replacement, $value);
    }

    return $value;
}

function kc_localized_url(string $locale, ?string $path = null): string {
    $locale = kc_normalize_locale($locale);
    $path = $path ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $query = $_GET;
    $query['lang'] = $locale;
    $queryString = http_build_query($query);

    return $path . ($queryString !== '' ? '?' . $queryString : '');
}

function kc_redirect_url_with_locale(string $path): string {
    return kc_localized_url(kc_current_locale(), $path);
}

function kc_language_switcher(string $class = ''): string {
    $current = kc_current_locale();
    $html = '<div class="' . htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" aria-label="' . htmlspecialchars(kc_t('common.language.label'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';

    foreach (kc_supported_locales() as $locale) {
        $active = $locale === $current;
        $classes = $active
            ? 'rounded-md bg-sky-500 px-2 py-1 text-xs font-semibold text-white'
            : 'rounded-md border border-slate-700 px-2 py-1 text-xs font-semibold text-slate-300 hover:border-sky-500 hover:text-sky-300';
        $html .= '<a class="' . $classes . '" href="' . htmlspecialchars(kc_localized_url($locale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" hreflang="' . $locale . '" lang="' . $locale . '">' . htmlspecialchars(kc_t('common.language.' . $locale), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    }

    return $html . '</div>';
}
