<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.kata_shotokan', '/kata-shotokan.php', [
    'definition',
    'learning',
    'rhythm',
    'evaluation',
]);
