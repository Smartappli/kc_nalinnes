<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.vocabulaire', '/vocabulaire-karate-shotokan.php', ['dojo', 'commands', 'techniques', 'counting']);
