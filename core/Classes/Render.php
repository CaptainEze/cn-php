<?php

namespace Core\Classes;

use eftec\bladeone\BladeOne;

/**
 * Uses the BladeOne templating engine.
 */
class Render
{
  private $viewsDir, $cacheDir, $instance;

  public function __construct(string $views_directory, string $cache_directory)
  {
    $this->viewsDir = $views_directory;
    $this->cacheDir = $cache_directory;

    $this->instance = new BladeOne($this->viewsDir, $this->cacheDir, BladeOne::MODE_AUTO);
  }

  public function render(string $file_name, array $context = []): void
  {
    echo $this->instance->run($file_name, $context);
  }
}
