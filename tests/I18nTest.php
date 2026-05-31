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

    public function testNewLocalesFallBackToTranslatedContent(): void {
        foreach (['bg', 'cs', 'da', 'el', 'ga', 'hr', 'hu', 'lt', 'lv', 'mt', 'ro', 'sk', 'sl', 'sv'] as $locale) {
            $this->assertSame('Meal reservation', kc_t('meal.hero.title', [], $locale));
        }
    }

    public function testTranslatedMealReservationLocalesUseNativeContent(): void {
        $this->assertSame('Essensreservierung', kc_t('meal.hero.title', [], 'de'));
        $this->assertSame('Reserva de comida', kc_t('meal.hero.title', [], 'es'));
        $this->assertSame('Prenotazione pasto', kc_t('meal.hero.title', [], 'it'));
        $this->assertSame('食事予約', kc_t('meal.hero.title', [], 'ja'));
        $this->assertSame('Rezerwacja posiłku', kc_t('meal.hero.title', [], 'pl'));
        $this->assertSame('Reserva de refeição', kc_t('meal.hero.title', [], 'pt'));
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
}
