<?php

/* Wrapper */
if (!function_exists('cache')) {
	function cache(string $cacheKey, Closure $closure, int $ttl = null)
	{
		if (!$cached = service('cache')->get($cacheKey)) {
			$cached = $closure();

			service('cache')->save($cacheKey, $cached, $ttl);
		}

		return $cached;
	}
}
