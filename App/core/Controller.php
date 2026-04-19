<?php

class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../src/Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo 'Vista no encontrada.';
            return;
        }

        require $viewFile;
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}
