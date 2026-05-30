<?php
declare(strict_types=1);

function compute_meal_total(int $adultQty, int $childQty, int $adultPrice = 19, int $childPrice = 10): int {
    $adultQty = max(0, $adultQty);
    $childQty = max(0, $childQty);
    return ($adultQty * $adultPrice) + ($childQty * $childPrice);
}

function ensure_meal_reservations_table(PDO $db): void {
    $db->exec('CREATE TABLE IF NOT EXISTS meal_reservations (id INT AUTO_INCREMENT PRIMARY KEY, member_user_id INT NOT NULL, profile_type VARCHAR(20) NOT NULL, dependent_id INT NULL, profile_name VARCHAR(255) NOT NULL, adult_qty INT NOT NULL DEFAULT 0, child_qty INT NOT NULL DEFAULT 0, total_amount DECIMAL(10,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
}

function ensure_meal_public_contact_columns(PDO $db): void {
    $columns = [
        'contact_email' => 'VARCHAR(255) NULL',
        'contact_phone' => 'VARCHAR(50) NULL',
        'notes' => 'TEXT NULL',
    ];

    foreach ($columns as $name => $definition) {
        try {
            $db->exec(sprintf('ALTER TABLE meal_reservations ADD COLUMN %s %s', $name, $definition));
        }
        catch (Throwable $e) {
            // Column already exists, or the database user cannot alter the table.
        }
    }
}
