<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public function __construct(private readonly Application $app)
    {
    }

    public function render(string $view, array $data = [], ?string $layout = null): string
    {
        $viewContent = $this->renderFile($view, $data);

        if ($layout === null) {
            return $viewContent;
        }

        return $this->renderFile($layout, array_merge($data, [
            'content' => $viewContent,
        ]));
    }

    public function partial(string $view, array $data = []): string
    {
        return $this->renderFile($view, $data);
    }

    private function renderFile(string $view, array $data = []): string
    {
        $path = $this->app->basePath('resources/views/' . $view . '.php');

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('View [%s] not found.', $view));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $path;

        return (string) ob_get_clean();
    }
}
