<?php
/**
 * header.php
 * Shared document head for the Client Portal pages.
 */
 $pageTitle = isset($pageTitle) ? $pageTitle : "Client Portal - LogixPulse";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>