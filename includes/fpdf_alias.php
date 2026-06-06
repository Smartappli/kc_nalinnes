<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

if (!class_exists('FPDF', false)) {
    class_alias(\Fpdf\Fpdf::class, 'FPDF');
}
