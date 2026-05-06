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
}
