<?php

class Controller {
    protected function render(string $view, array $data = []): void {
        $viewFile = __DIR__ . '/../src/Views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../src/Views/layout/base.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo 'Vista no encontrada.';
            return;
        }

        if (!file_exists($layoutFile)) {
            http_response_code(500);
            echo 'Layout base no encontrado.';
            return;
        }

        $pageTitle = $data['pageTitle'] ?? 'Concesionario';
        $showNav = $data['showNav'] ?? true;
        $message = $data['message'] ?? '';
        $error = $data['error'] ?? '';
        $contentTemplate = $viewFile;
        $contentData = $data;

        require $layoutFile;
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}
