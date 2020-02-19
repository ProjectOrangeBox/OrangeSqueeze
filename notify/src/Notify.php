<?php

namespace projectorangebox\notify;

use Exception;
use projectorangebox\session\SessionInterface;

class Notify implements NotifyInterface
{
	protected $sessionService;

	protected $keep = false;

	public function __construct(array $config, SessionInterface $sessionService)
	{
		$this->config = $config;

		$defaults = [
			'key' => 'notify::key',
			'default status' => 'warn',
			'site url' => '',
			'as' => 'json',
		];

		$this->config = array_merge($defaults, $this->config);

		$this->as($this->config['as']);

		$this->sessionService = $sessionService;
	}

	public function add(string $msg, string $status = null, array $payload = []): NotifyInterface
	{
		$status = $status ?? $this->config['default status'];

		$current = $this->sessionService->get($this->config['key'], []);

		$payload['msg'] = $msg;
		$payload['status'] = $status;

		$current[$msg . $status] = $payload;

		$this->sessionService->set($this->config['key'], $current);

		return $this;
	}

	public function clear(): NotifyInterface
	{
		$this->sessionService->set($this->config['key'], []);

		return $this;
	}

	public function as(string $as): NotifyInterface
	{
		if (!\in_array($as, ['debug', 'array', 'json', 'html'])) {
			throw new Exception('Unsupport Notify response type "' . $as . '".');
		}

		$this->config['as'] = \ucfirst(\strtolower($as));

		return $this;
	}

	public function keep(): NotifyInterface
	{
		$this->keep = true;

		return $this;
	}

	public function get(string $param = null)
	{
		$as = 'as' . $this->config['as'];

		return ($param) ? $this->$as($param) : $this->$as();
	}

	public function redirect(string $uri = '', string $method = 'auto', int $code = NULL): void
	{
		if (!preg_match('#^(\w+:)?//#i', $uri)) {
			$uri = $this->config['site url'] . $uri;
		}

		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
			$method = 'refresh';
		} elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
				// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
			} else {
				$code = 302;
			}
		}

		switch ($method) {
			case 'refresh':
				header('Refresh:0;url=' . $uri);
				break;
			default:
				header('Location: ' . $uri, TRUE, $code);
				break;
		}

		exit($code - 200);
	}

	/* protected */

	protected function asArray(): array
	{
		return $this->_get();
	}

	protected function asJson(): string
	{
		return \json_encode($this->asArray());
	}

	protected function asHtml(string $format = '<div class="notify notify-{status}">{msg}</div>'): string
	{
		$html = '';

		foreach ($this->asArray() as $record) {
			$line = $format;

			/* simple merge */
			foreach ($record as $key => $value) {
				$line = str_replace('{' . $key . '}', $value, $line);
			}

			$html .= $line . PHP_EOL;
		}

		return $html;
	}

	protected function asDebug(): string
	{
		ob_start();

		var_dump($this->asArray());

		return ob_get_clean();
	}

	protected function _get(): array
	{
		$current = array_values($this->sessionService->get($this->config['key'], []));

		if (!$this->keep) {
			$this->clear();

			$this->keep = false;
		}

		return $current;
	}
} /* end class */
