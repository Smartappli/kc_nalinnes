<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/fpdf_alias.php';

use setasign\Fpdi\Fpdi;

final class LicencePdfFiller extends Fpdi
{
    /**
     * Écrit un texte dans une case/zone.
     */
    public function writeText(
        float $x,
        float $y,
        string $text,
        int $size = 10
    ): void {
        $this->SetFont('Arial', '', $size);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($x, $y);
        $this->Write(0, $text);
    }

    /**
     * Écrit un texte centré dans une largeur donnée.
     */
    public function writeCentered(
        float $x,
        float $y,
        float $w,
        string $text,
        int $size = 10
    ): void {
        $this->SetFont('Arial', '', $size);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($x, $y);
        $this->Cell($w, 5, $text, 0, 0, 'C');
    }

    /**
     * Coche une case.
     */
    public function checkBox(float $x, float $y, int $size = 12): void
    {
        $this->SetFont('Arial', 'B', $size);
        $this->SetXY($x, $y);
        $this->Cell(4, 4, 'X', 0, 0, 'C');
    }
}

$inputFile = __DIR__ . '/fichier_modulable_licence_pratiquant_avec_carnet.pdf';
$outputFile = __DIR__ . '/licence_remplie.pdf';

/**
 * Données à injecter dans le formulaire.
 */
$data = [
    'nom' => 'DEBAUCHE',
    'prenoms' => 'Olivier',
    'sexe' => 'M', // M ou F
    'age' => '45',
    'date_naissance_jour' => '12',
    'date_naissance_mois' => '03',
    'date_naissance_annee' => '1980',
    'lieu_naissance' => 'Charleroi',
    'nationalite' => 'Belge',
    'carte_identite' => '1234567890',
    'profession' => 'Enseignant',
    'adresse' => 'Rue Exemple 10',
    'numero' => '10',
    'code_postal' => '6001',
    'commune' => 'Marcinelle',
    'boite' => '',
    'email' => 'olivier@example.com',
    'date_signature_lieu' => 'Marcinelle',
    'date_signature_jour' => '05',
    'date_signature_mois' => '04',
    'date_signature_annee' => '2026',
    'demande_carnet' => true,

    // Parties du bas
    'licence_numero' => '2026-000123',
    'nom_bas' => 'DEBAUCHE',
    'prenom_bas' => 'Olivier',
    'adresse_bas' => 'Rue Exemple 10, 6001 Marcinelle',
    'membre_jusquau' => '31/12/2026',
];

/**
 * Création du PDF.
 */
$pdf = new LicencePdfFiller();

// Récupère la taille réelle de la page importée
$pageCount = $pdf->setSourceFile($inputFile);
$templateId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($templateId);

$orientation = $size['width'] > $size['height'] ? 'L' : 'P';

$pdf->AddPage($orientation, [$size['width'], $size['height']]);
$pdf->useTemplate($templateId);

/**
 * IMPORTANT :
 * Les coordonnées ci-dessous sont à ajuster légèrement selon votre rendu.
 * Unités FPDI/FPDF ici : millimètres.
 *
 * Astuce :
 * - faites un premier export,
 * - regardez le PDF,
 * - corrigez les X/Y de 1 ou 2 mm si nécessaire.
 */

// -------------------------
// Zone principale du haut
// -------------------------

$pdf->writeText(20, 82, $data['nom'], 11);               // NOM
$pdf->writeText(20, 95, $data['prenoms'], 11);           // Prénoms

if ($data['sexe'] === 'M') {
    $pdf->checkBox(147, 95, 11); // M
} elseif ($data['sexe'] === 'F') {
    $pdf->checkBox(154, 95, 11); // F
}

$pdf->writeText(171, 95, $data['age'], 11);              // Age

$pdf->writeText(58, 107, $data['date_naissance_jour'], 11);
$pdf->writeText(79, 107, $data['date_naissance_mois'], 11);
$pdf->writeText(101, 107, $data['date_naissance_annee'], 11);

$pdf->writeText(124, 107, $data['lieu_naissance'], 11);

$pdf->writeText(34, 119, $data['nationalite'], 11);
$pdf->writeText(87, 119, $data['carte_identite'], 11);
$pdf->writeText(166, 119, $data['profession'], 11);

$pdf->writeText(32, 132, $data['adresse'], 11);          // Adresse
$pdf->writeText(201, 132, $data['numero'], 11);          // N°

$pdf->writeText(34, 144, $data['code_postal'], 11);      // Code postal
$pdf->writeText(77, 144, $data['commune'], 11);          // Commune
$pdf->writeText(205, 144, $data['boite'], 11);           // Bte

$pdf->writeText(46, 156, $data['email'], 11);            // Email

// -------------------------
// Signature / lieu / date
// -------------------------

$pdf->writeText(54, 214, $data['date_signature_lieu'], 10);
$pdf->writeText(94, 214, $data['date_signature_jour'], 10);
$pdf->writeText(112, 214, $data['date_signature_mois'], 10);
$pdf->writeText(130, 214, $data['date_signature_annee'], 10);

// Demande carnet : oui / non
if ($data['demande_carnet'] === true) {
    $pdf->checkBox(108, 221, 10); // oui
} else {
    $pdf->checkBox(125, 221, 10); // non
}

// -------------------------
// 3 volets du bas
// -------------------------

$blocks = [
    ['x' => 8],    // reçu association
    ['x' => 82],   // duplicata cercle
    ['x' => 159],  // à coller dans le carnet
];

foreach ($blocks as $block) {
    $x = $block['x'];

    $pdf->writeText($x + 10, 277, $data['licence_numero'], 10);   // Licence N°
    // zone cachet du cercle laissée vide volontairement
    $pdf->writeText($x + 10, 314, $data['nom_bas'], 10);          // *Nom
    $pdf->writeText($x + 10, 326, $data['prenom_bas'], 10);       // *Prénom
    $pdf->writeText($x + 10, 338, $data['adresse_bas'], 10);      // *Adresse
    $pdf->writeText($x + 20, 371, $data['membre_jusquau'], 10);   // Jusqu'au
}

// Export
$pdf->Output('F', $outputFile);

echo kc_t('fill_licence.pdf_generated', ['path' => $outputFile]) . PHP_EOL;
