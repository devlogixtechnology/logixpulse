<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

require_auth();

json_success([
    'csrf_token' => csrf_token(),
]);
