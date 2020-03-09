<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\response;

use projectorangebox\response\Response;
use projectorangebox\cache\CacheInterface;
use projectorangebox\response\ResponseInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class ResponseCached extends Response implements ResponseInterface
{
	protected $cacheExpiration = 0;

	public function __construct(array &$config)
	{
		parent::__construct($config);

		$this->cacheExpiration = $this->config['cache expiration'] ?? 0;
	}

	public function cache(int $time = null)
	{
		$this->cacheExpiration = $time ?? 0;

		return $this;
	}

	public function display(string $output = null, int $statusCode = 0): void
	{
		if ($output) {
			$this->finalOutput .= $output;
		}

		if ($this->cacheExpiration > 0) {
			$this->writeCache($statusCode);
		}

		if (!$this->headerSend) {
			$this->sendHeader();
		}

		/* final echo */
		echo $this->finalOutput;

		$this->exit($statusCode);
	}

	protected function writeCache(int $statusCode): void
	{
		$cacheService = $this->config['cacheService'];

		if ($cacheService instanceof CacheInterface) {
			$cache['key'] = $this->config['uri'];
			$cache['finalOutput'] = $this->finalOutput;
			$cache['header'] = $this->header;
			$cache['http_response_code'] = $this->http_response_code;
			$cache['statusCode'] = $statusCode;

			$cacheService->save($this->getKey($this->config['uri']), $cache, $this->cacheExpiration);
		} else {
			\log_message('error', __METHOD__ . ' CacheService is not an instance of CacheInterface.');
		}
	}

	public function displayCache($uri): void
	{
		$cacheService = $this->config['cacheService'];

		if ($cacheService instanceof CacheInterface) {
			if (is_array($cache = $cacheService->get($this->getKey($uri)))) {
				$this->header = $cache['header'];
				$this->http_response_code = $cache['http_response_code'];

				$this->sendHeader();

				/* final echo */
				echo $cache['finalOutput'];

				$this->exit($cache['statusCode']);
			}
		} else {
			\log_message('error', __METHOD__ . ' CacheService is not an instance of CacheInterface.');
		}
	}

	protected function getKey(string $uri): string
	{
		return 'response.output.' . md5($uri);
	}
} /* end class */
