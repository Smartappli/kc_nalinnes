<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.techniques_kumite', '/techniques_kumite.php', [
    'distance',
    'timing',
    'control',
    'respect',
]);
