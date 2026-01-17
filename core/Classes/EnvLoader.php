<?php

namespace Core\Classes;

/**
 * This class loads environment variables from a .env file.
 * It makes them available through the $_ENV superglobal.
 */
class EnvLoader
{
	public static function loadEnv($file): void
	{
		if (file_exists($file)) {
			$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach ($lines as $line) {
				if (strpos($line, '#') === 0) {
					continue;
				}
				list($key, $value) = explode('=', $line, 2);
				$_ENV[$key] = $value;
			}
		} else {
			throw new \Exception("Env file not found");
		}
	}
}
