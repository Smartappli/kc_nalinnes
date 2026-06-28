<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../member/pdf_access.php';

final class MemberPdfAccessTest extends TestCase {
    public function testBuildAndValidateDownloadToken(): void {
        $token = build_download_token('42', 'csrf-secret');
        $this->assertTrue(is_valid_download_token($token, '42', 'csrf-secret'));
        $this->assertFalse(is_valid_download_token($token, '43', 'csrf-secret'));
    }

    public function testIsAllowedTemplateUsesWhitelistAndBasename(): void {
        $templates = ['mutualia-ac-sport-fr.pdf'];
        $this->assertTrue(is_allowed_template('mutualia-ac-sport-fr.pdf', $templates));
        $this->assertTrue(is_allowed_template('../mutualia-ac-sport-fr.pdf', $templates));
        $this->assertFalse(is_allowed_template('unknown.pdf', $templates));
    }

    public function testPrecompletedMutuelleFilenameIsSafe(): void {
        $this->assertSame('mutuelle-precomplete-jean-dupont.pdf', precompleted_mutuelle_filename('Jean Dupont'));
        $this->assertSame('mutuelle-precomplete-membre.pdf', precompleted_mutuelle_filename(''));
    }

    public function testDefaultMutuelleTemplatesExposeMappedDocs(): void {
        $templates = default_mutuelle_pdf_templates();

        $this->assertContains('mutualia-ac-sport-fr.pdf', $templates);
        $this->assertContains('mc_formulaire_AC_SPORT_A4_FR_2024_V2.pdf', $templates);
        $this->assertContains('Formulaire-de-demande-dintervention-Sports-2025.pdf', $templates);
    }

    public function testMutuellePdfContextUsesMemberAndClubDefaults(): void {
        $context = mutuelle_pdf_context('Jean Dupont', 'Parent Dupont', [
            'membership_year' => '2026',
            'beneficiary_birthdate' => '2012-04-12',
        ]);

        $this->assertSame('Jean Dupont', $context['beneficiary_name']);
        $this->assertSame('Parent Dupont', $context['responsible_name']);
        $this->assertSame('2012-04-12', $context['beneficiary_birthdate']);
        $this->assertSame('2026', $context['membership_year']);
        $this->assertSame('KC Nalinnes', $context['club_name']);
    }

    public function testUnknownMutuelleTemplateFallsBackToGenericOverlay(): void {
        $definition = mutuelle_pdf_template_definition('unknown.pdf');

        $this->assertCount(3, $definition['fields']);
        $this->assertSame('Beneficiaire', $definition['fields'][0]['label']);
    }
}
