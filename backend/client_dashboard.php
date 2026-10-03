<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireLogin('client');

// Redirect to full rich client portal dashboard
header('Location: ../client-portal-dashboard/index.php');
exit;
