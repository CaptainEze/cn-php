<?php

use Core\Classes\Router;
use Core\Classes\Render;

require_once __DIR__ . "/core/bootstrap.php";

$render = new Render(__DIR__ . "/views", __DIR__ . "/cache");

Router::serveDir("/"); // Serve from public root

Router::new("GET", "/", function () use ($render): void {
    $render->render("home");
});




Router::matchRoute();