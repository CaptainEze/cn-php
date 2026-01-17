<?php

namespace Core\Classes;

/**
 * This class provides a simple file-based cache.
 */
class Cache
{
  private static string $cacheDir = "";
  private static int $defaultTtl = 3600;

  /**
   * Prepare cache directory.
   */
  public static function init(?string $cacheDir = null, ?int $defaultTtl = null): void
  {
    if ($cacheDir !== null) {
      self::$cacheDir = rtrim($cacheDir, "/");
    } else {
      self::$cacheDir = rtrim($_SERVER['DOCUMENT_ROOT'], "/") . "/cache";
    }
    if ($defaultTtl !== null) {
      self::$defaultTtl = $defaultTtl;
    }
    if (!is_dir(self::$cacheDir)) {
      mkdir(self::$cacheDir, 0755, true);
    }
  }

  private static function getFilePath(string $key): string
  {
    return self::$cacheDir . '/' . md5($key) . 'cache';
  }

  public static function set(string $key, mixed $value, ?int $ttl = null): void
  {
    $data = [
      'expiry' => time() + ($ttl ?? self::$defaultTtl),
      'value' => $value
    ];
    file_put_contents(self::getFilePath($key), serialize($data));
  }

  public static function get(string $key): mixed
  {
    $file = self::getFilePath($key);
    if (!file_exists($file)) return null;

    $data = unserialize(file_get_contents($file));
    if ($data['expiry'] < time()) {
      unlink($file);
      return null;
    }

    return $data['value'];
  }

  public static function has(string $key): bool
  {
    $file = self::getFilePath($key);
    if (!file_exists($file)) return false;

    $data = unserialize(file_get_contents($file));
    if ($data['expiry'] < time()) {
      unlink($file);
      return false;
    }
    return true;
  }

  public static function delete(string $key): void
  {
    $file = self::getFilePath($key);
    if (file_exists($file)) {
      unlink($file);
    }
  }

  public static function clear(): void
  {
    foreach (glob(self::$cacheDir . '/*.cache') as $file) {
      unlink($file);
    }
  }
}
