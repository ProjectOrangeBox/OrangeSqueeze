<?php

namespace projectorangebox\router;

use projectorangebox\cache\CacheInterface;

class RouterBuilder implements RouterBuilderInterface
{
	protected $config = [];
	protected $routes = [];
	protected $cache;
	protected $cacheKey = '';
	protected $re = '/<(.[^>]*)>/m';

	public function __construct(array $config, string $key, CacheInterface $cache = null)
	{
		$this->config = $config;

		$this->config['default'] = $this->config['default'] ?? ['get', 'cli'];
		$this->config['all'] = $this->config['all'] ?? ['get', 'cli', 'post', 'put', 'delete'];

		$this->routes = $this->config[$key];
		$this->cacheKey = 'RouterBuilder.' . $key;
		$this->cache = $cache;
	}

	public function build(): array
	{
		/* are we caching or rebuilding everytime? */
		if ($this->cache) {
			if (!$routes = $this->cache->get($this->cacheKey)) {
				$routes = $this->format($this->routes);

				$this->cache->save($this->cacheKey, $routes);
			}
		} else {
			$routes = $this->format($this->routes);
		}

		return $routes;
	}

	protected function format(array $routes): array
	{
		$formatted = [];

		foreach ($routes as $regex => $rewrite) {
			/* regex passed by reference */
			$httpMethod = $this->GetMethods($regex);

			if (preg_match_all($this->re, $regex, $matches)) {
				foreach ($matches[0] as $idx => $match) {
					/* (?<folder>[^/]*) */
					$regex = str_replace($match, '(?<' . $matches[1][$idx] . '>[^/]*)', $regex);
				}
			}

			$regex = '#^/' . ltrim($regex, '/') . '$#im';

			foreach ($httpMethod as $method) {
				$formatted[$method][$regex] = $rewrite;
			}
		}

		return $formatted;
	}

	protected function GetMethods(&$regex): array
	{
		/* default */
		$httpMethods = $this->config['default'];

		if ($regex[0] == '@') {
			$firstSlash = \strpos($regex, '/');
			$methods = substr($regex, 1, $firstSlash - 1);
			$regex = \substr($regex, $firstSlash);

			if (strlen($methods)) {
				/* use supplied */
				$httpMethods = explode(',', $methods);
			} else {
				/* nothing specific supplied */
				$httpMethods = $this->config['all'];
			}
		}

		return $httpMethods;
	}
}
