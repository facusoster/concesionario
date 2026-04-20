<?php
$contentTemplate = $contentTemplate ?? null;
$contentData = $contentData ?? [];
$pageTitle = $pageTitle ?? 'Concesionario';
$showNav = $showNav ?? true;

if ($contentTemplate === null || !file_exists($contentTemplate)) {
    http_response_code(500);
    echo 'Template de contenido no encontrado.';
    return;
}

require __DIR__ . '/header.php';
require __DIR__ . '/nav.php';
?>
<main class="container page-shell pb-4">
    <?php require __DIR__ . '/alerts.php'; ?>
    <?php
    extract($contentData);
    require $contentTemplate;
    ?>
</main>
<?php require __DIR__ . '/footer.php';
