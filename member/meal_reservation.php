<?php
declare(strict_types=1);

function compute_meal_total(int $adultQty, int $childQty, int $adultPrice = 19, int $childPrice = 10): int {
    $adultQty = max(0, $adultQty);
    $childQty = max(0, $childQty);
    return ($adultQty * $adultPrice) + ($childQty * $childPrice);
}
