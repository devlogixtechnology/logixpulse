<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/../.env');

require_once __DIR__ . '/session.php';
start_secure_session();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../middleware/auth_check.php';
