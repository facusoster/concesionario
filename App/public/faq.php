<?php
// FAQ integrada: renderiza solo el bloque delimitado en README.md
require_once __DIR__ . '/../config/app.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/**
 * Convierte una linea Markdown basica en HTML seguro para inline.
 */
function renderInlineMarkdown(string $text): string
{
    $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $safe = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $safe) ?? $safe;
    $safe = preg_replace('/`([^`]+)`/', '<code>$1</code>', $safe) ?? $safe;

    return $safe;
}

/**
 * Renderizador Markdown minimo (headings, listas y parrafos).
 */
function renderSimpleMarkdown(string $markdown): string
{
    $lines = preg_split('/\R/', $markdown) ?: [];
    $html = '';
    $inList = false;

    foreach ($lines as $line) {
        $trim = trim($line);

        if ($trim === '') {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            continue;
        }

        if (preg_match('/^###\s+(.+)$/', $trim, $matches)) {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $html .= '<h3>' . renderInlineMarkdown($matches[1]) . "</h3>\n";
            continue;
        }

        if (preg_match('/^##\s+(.+)$/', $trim, $matches)) {
            if ($inList) {
                $html .= "</ul>\n";
                $inList = false;
            }
            $html .= '<h2>' . renderInlineMarkdown($matches[1]) . "</h2>\n";
            continue;
        }

        if (preg_match('/^-\s+(.+)$/', $trim, $matches)) {
            if (!$inList) {
                $html .= "<ul>\n";
                $inList = true;
            }
            $html .= '<li>' . renderInlineMarkdown($matches[1]) . "</li>\n";
            continue;
        }

        if ($inList) {
            $html .= "</ul>\n";
            $inList = false;
        }

        $html .= '<p>' . renderInlineMarkdown($trim) . "</p>\n";
    }

    if ($inList) {
        $html .= "</ul>\n";
    }

    return $html;
}

$readmePath = __DIR__ . '/../README.md';
$faqStart = '<!-- FAQ:START -->';
$faqEnd = '<!-- FAQ:END -->';
$faqHtml = '';

if (is_file($readmePath) && is_readable($readmePath)) {
    $content = file_get_contents($readmePath);

    if (is_string($content)) {
        $startPos = strpos($content, $faqStart);
        $endPos = strpos($content, $faqEnd);

        if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
            $startPos += strlen($faqStart);
            $faqMarkdown = trim(substr($content, $startPos, $endPos - $startPos));
            $faqHtml = renderSimpleMarkdown($faqMarkdown);
        }
    }
}

if ($faqHtml === '') {
    $faqHtml = '<p>No se encontró contenido FAQ en README.md.</p>';
}

$pageTitle = 'FAQ - Concesionario';
$showNav = true;
$message = '';
$error = '';
$contentTemplate = __DIR__ . '/../src/Views/public/faq_content.php';
$contentData = [
    'faqHtml' => $faqHtml,
];

require __DIR__ . '/../src/Views/layout/base.php';
