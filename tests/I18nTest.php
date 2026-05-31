<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/i18n.php';

final class I18nTest extends TestCase {
    public function testNormalizeLocaleKeepsSupportedLanguages(): void {
        $this->assertSame('fr', kc_normalize_locale('fr-BE'));
        $this->assertSame('en', kc_normalize_locale('en_US'));
        $this->assertSame('nl', kc_normalize_locale('nl'));
        $this->assertSame('ja', kc_normalize_locale('ja-JP'));
        $this->assertSame('bg', kc_normalize_locale('bg'));
    }

    public function testNormalizeLocaleFallsBackToFrench(): void {
        $this->assertSame('fr', kc_normalize_locale('xx'));
        $this->assertSame('fr', kc_normalize_locale(null));
    }

    public function testTranslationUsesRequestedLocale(): void {
        $this->assertSame('Meal reservation', kc_t('meal.hero.title', [], 'en'));
        $this->assertSame('Maaltijdreservatie', kc_t('meal.hero.title', [], 'nl'));
    }

    public function testTranslationFallsBackToFrenchForUnknownLocale(): void {
        $this->assertSame('Réservation repas', kc_t('meal.hero.title', [], 'xx'));
    }

    public function testMemberDashboardTranslationsAreAvailable(): void {
        $this->assertSame('Member dashboard', kc_t('member.heading', [], 'en'));
        $this->assertSame('Ledendashboard', kc_t('member.heading', [], 'nl'));
    }

    public function testEveryLocaleLoadsEveryTranslationModule(): void {
        foreach (kc_supported_locales() as $locale) {
            foreach (kc_translation_modules() as $module) {
                $this->assertNotSame([], kc_load_translation_file($locale, $module), $locale . '/' . $module);
            }
        }
    }

    public function testTranslatedMealReservationLocalesUseNativeContent(): void {
        $expected = [
            'bg' => 'Резервация за хранене',
            'cs' => 'Rezervace jídla',
            'da' => 'Måltidsreservation',
            'de' => 'Essensreservierung',
            'el' => 'Κράτηση γεύματος',
            'es' => 'Reserva de comida',
            'et' => 'Söögi broneerimine',
            'fi' => 'Ateriavaraus',
            'ga' => 'Áirithint béile',
            'hr' => 'Rezervacija obroka',
            'hu' => 'Étkezésfoglalás',
            'it' => 'Prenotazione pasto',
            'ja' => '食事予約',
            'lt' => 'Valgio rezervacija',
            'lv' => 'Maltītes rezervācija',
            'mt' => 'Riservazzjoni tal-ikla',
            'pl' => 'Rezerwacja posiłku',
            'pt' => 'Reserva de refeição',
            'ro' => 'Rezervare masă',
            'sk' => 'Rezervácia jedla',
            'sl' => 'Rezervacija obroka',
            'sv' => 'Måltidsbokning',
        ];

        foreach ($expected as $locale => $title) {
            $this->assertSame($title, kc_t('meal.hero.title', [], $locale), $locale);
        }
    }

    public function testLocaleLabelsContainJapaneseAndEuLanguages(): void {
        $labels = kc_locale_labels();

        $this->assertSame('日本語', $labels['ja']);
        $this->assertSame('Български', $labels['bg']);
        $this->assertSame('Gaeilge', $labels['ga']);
        $this->assertSame('Malti', $labels['mt']);
        $this->assertCount(25, kc_supported_locales());
    }

    public function testLocalizedUrlKeepsExistingQueryAndChangesLanguage(): void {
        $_GET = ['foo' => 'bar', 'lang' => 'fr'];

        $this->assertSame('/reservation-repas.php?foo=bar&lang=nl', kc_localized_url('nl', '/reservation-repas.php'));
    }

    public function testLanguageSwitcherSubmitsCurrentPageWithSelectedLocale(): void {
        $_SERVER['REQUEST_URI'] = '/reservation-repas.php?foo=bar&lang=fr';
        $_GET = ['foo' => 'bar', 'lang' => 'fr'];

        $html = kc_language_switcher('test-switcher');

        $this->assertStringContainsString('action="/reservation-repas.php"', $html);
        $this->assertStringContainsString('name="foo" value="bar"', $html);
        $this->assertStringContainsString('name="lang"', $html);
        $this->assertStringContainsString('value="fr" lang="fr" selected', $html);
        $this->assertStringContainsString('onchange="this.form.submit();"', $html);
    }
}
