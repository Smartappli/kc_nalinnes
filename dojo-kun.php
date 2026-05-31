<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.dojo_kun', '/dojo-kun.php', ['meaning', 'principles', 'practice', 'club']);
