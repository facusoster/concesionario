<?php
$pageTitle = $pageTitle ?? 'Concesionario';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Google Fonts preconnect and Inter family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .page-shell {
            min-height: calc(100vh - 140px);
        }
        .form-label .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
<header class="bg-white border-bottom py-3">
    <div class="container text-center">
        <h1 class="h4 mb-0">Sistema Concesionario</h1>
    </div>
</header>
