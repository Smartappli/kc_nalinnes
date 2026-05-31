<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.karate_shotokan', '/karate-shotokan.php', [
    'origin',
    'training',
    'values',
    'progression',
]);
