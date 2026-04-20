<?php
$pageTitle = $pageTitle ?? 'Concesionario';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
    <div class="container">
        <h1 class="h4 mb-0">Sistema Concesionario</h1>
    </div>
</header>
