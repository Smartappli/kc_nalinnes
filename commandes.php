<?php
declare(strict_types=1);

require __DIR__ . '/includes/i18n.php';

$locale = kc_current_locale();

function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="<?= e($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(kc_t('commandes.meta.title')) ?></title>
</head>
<body>
<form method="post">
    <h1><?= e(kc_t('commandes.heading')) ?></h1>
    <h2><?= e(kc_t('commandes.date')) ?></h2>
    <br>
    <?= e(kc_t('commandes.place')) ?><br>
    <br>
    <strong><?= e(kc_t('commandes.menu.title')) ?></strong>
    <table>
        <tr>
            <th><?= e(kc_t('commandes.table.description')) ?></th>
            <th><?= e(kc_t('commandes.table.price')) ?></th>
            <th><?= e(kc_t('commandes.table.quantity')) ?></th>
            <th><?= e(kc_t('commandes.table.total')) ?></th>
        </tr>
        <tr>
            <td><?= e(kc_t('commandes.menu.adults')) ?></td>
            <td>17</td>
            <td><input type="text" name="repas_adulte" value="0"></td>
            <td><span id="total_repas_adulte">0</span></td>
        </tr>
        <tr>
            <td><?= e(kc_t('commandes.menu.children')) ?></td>
            <td>10</td>
            <td><input type="text" name="repas_enfant" value="0"></td>
            <td><span id="total_repas_enfant">0</span></td>
        </tr>
    </table>
    <br>
    <strong><?= e(kc_t('commandes.reservation.title')) ?></strong>
    <p><label><?= e(kc_t('commandes.form.last_name')) ?> : </label><input type="text" name="nom" placeholder="<?= e(kc_t('commandes.form.last_name_placeholder')) ?>"></p>
    <p><label><?= e(kc_t('commandes.form.first_name')) ?> : </label><input type="text" name="prenom" placeholder="<?= e(kc_t('commandes.form.first_name_placeholder')) ?>"></p>
    <p><label><?= e(kc_t('commandes.form.total_to_pay')) ?> : </label><span id="Total_a_payer">0</span> EUR</p>
    <br>
    <input type="submit" value="<?= e(kc_t('commandes.form.submit')) ?>">
</form>
</body>
</html>
