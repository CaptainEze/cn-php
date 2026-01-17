<?php

namespace Core\Classes;

/**
 * This class handles routing for the application.
 */
class Router
{
  protected static $routes = [];
  protected static $publicDirs = [];

  public static function new(string $method, string $url, \Closure $action): void
  {
    self::$routes[$method][$url] = $action;
  }

  public static function serveDir(string $path): void
  {
    array_push(self::$publicDirs, $path);
  }

  public static function exit404(): never
  {
    header("HTTP/1.0 404 Not Found");
    exit();
  }

  public static function getContentType(string $filePath): string
  {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
      'css' => 'text/css',
      'js' => 'application/javascript',
      'html' => 'text/html',
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'gif' => 'image/gif',
      'svg' => 'image/svg+xml',
      'json' => 'application/json',
      'xml' => 'application/xml',
      'txt' => 'text/plain',
      'pdf' => 'application/pdf'
    ];

    return $mimeTypes[$extension] ?? 'application/octet-stream'; // Default binary stream if unknown
  }


  public static function matchRoute(): void
  {
    $method = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];
    $urlParts = parse_url($url);
    $path = rtrim($urlParts['path'], '/');

    if ($path === '') {
      $path = '/';
    }

    if (isset(self::$routes[$method])) {
      foreach (self::$routes[$method] as $routeUrl => $target) {
        $pattern = preg_replace('/\/:([^\/?]+)/', '/(?P<$1>[^/]+)', $routeUrl);

        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
          // Extract parameter names from the route URL
          preg_match_all('/\/:([^\/?]+)/', $routeUrl, $paramNames);

          // Build ordered parameters array
          $params = [];
          foreach ($paramNames[1] as $paramName) {
            if (isset($matches[$paramName])) {
              $params[] = $matches[$paramName];
            }
          }

          call_user_func_array($target, $params);
          return;
        }
      }
      $altPath = "$path/";
      if (isset(self::$routes[$method][$altPath])) {
        call_user_func(self::$routes[$method][$altPath]);
        return;
      }
    }

    $publicBase = realpath($_SERVER['DOCUMENT_ROOT'] . '/public');
    $safePath = realpath($publicBase . '/' . ltrim($path, '/'));

    if ($safePath && str_starts_with($safePath, $publicBase) && is_file($safePath)) {
      if (file_exists(filename: $safePath)) {
        $contentType = self::getContentType($safePath);
        header("Content-Type: $contentType");
        readfile($safePath);
        return;
      } else {
        self::exit404();
      }
    }

    self::exit404();
  }
}
