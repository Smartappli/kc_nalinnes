<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../member/meal_reservation.php';

final class MealReservationTest extends TestCase {
    public function testComputeMealTotalWithDefaultPrices(): void {
        $this->assertSame(58, compute_meal_total(2, 2));
    }

    public function testComputeMealTotalNeverUsesNegativeQuantities(): void {
        $this->assertSame(10, compute_meal_total(-2, 1));
        $this->assertSame(0, compute_meal_total(-1, -1));
    }

    public function testComputeMealTotalWithCustomPrices(): void {
        $this->assertSame(42, compute_meal_total(2, 1, 16, 10));
    }
}
