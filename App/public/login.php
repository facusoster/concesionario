<?php
// Simple login form (formulario de login)
// Archivo público: public/login.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/Flash.php';

$message = Flash::get('success') ?? Flash::get('info') ?? '';
$error = Flash::get('error') ?? '';

$pageTitle = 'Login - Concesionario';
$showNav = false;
$contentTemplate = __DIR__ . '/../src/Views/public/login_content.php';
$contentData = [];

require __DIR__ . '/../src/Views/layout/base.php';