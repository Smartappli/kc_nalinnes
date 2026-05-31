<?php
declare(strict_types=1);

require __DIR__ . '/includes/static_page.php';

kc_render_static_page('page.technique_base', '/technique_base.php', ['stances', 'blocks', 'strikes', 'coordination']);
