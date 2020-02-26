<?php

namespace projectorangebox\formatter;

use FS;
use Exception;

class formatter implements formatterInterface
{
	/* default server content type */
	protected $contentType = 'text/html';

	/* default exit code */
	protected $exitCode = 0;

	/* default server status code */
	protected $statusCode = 200;

	/* prefix when trying to load a format/view */
	protected $formatFilePrefix = '';

	/* all known format/view files */
	protected $formatFiles = [];

	/* convert simple "type" into server content type response */
	protected $typeMap = [
		'ajax' => 'application/json',
		'json' => 'application/json',
		'html' => 'text/html',
		'cli' => 'text/html',
	];

	/* default charset */
	protected $charset = 'UTF-8';

	protected $send = false;

	/**
	 * __construct
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->typeMap = $config['type map'] ?? $this->typeMap;

		$this->type(($config['type'] ?? 'html'), ($config['charset'] ?? $this->charset));
		$this->code($config['status code'] ?? $this->statusCode);

		$this->formatFilePrefix = '/' . trim($config['format file prefix'], '/') . '/';
		$this->formatFiles = $config['format files'] ?? [];

		/* override auto set */
		$this->exitCode = $config['exit code'] ?? $this->exitCode;
		$this->contentType = $config['content type'] ?? $this->contentType;
	}

	public function send(int $statusCode, string $type, string $charset = 'UTF-8'): formatterInterface
	{
		$this->send = true;

		$this->code($statusCode);
		$this->type($type, $charset);

		return $this;
	}

	public function format(array $array = [], string $view = null): string
	{
		return ($this->send) ? $this->_display($array, $view) : $this->_format($array, $view);
	}

	protected function type(string $type, string $charset = 'UTF-8'): void
	{
		if (!isset($this->typeMap[$type])) {
			throw new Exception('Unknown Content Type.');
		}

		$this->contentType = $this->typeMap[$type];
		$this->charset = $charset;
	}

	protected function code(int $statusCode): void
	{
		$this->statusCode = abs($statusCode);

		if ($this->statusCode < 100) {
			$this->exitCode = $this->statusCode + 9;
			$this->statusCode = 500;
		} else {
			$this->exitCode = 1;
		}
	}

	protected function _format(array $array = [], string $view = null): string
	{
		$view = trim($this->formatFilePrefix . ($view ?? $this->statusCode), '/');

		if (!\array_key_exists($view, $this->formatFiles)) {
			throw new Exception('Format File "' . $view . '" Not Found.');
		}

		return $this->_view($this->formatFiles[$view], $array);
	}

	protected function _display(array $array = [], string $view = null): void
	{
		if ($this->contentType) {
			header("Content-type: " . $this->contentType . '; charset=UTF-8');
		}

		if ($this->statusCode > 0) {
			http_response_code($this->statusCode);
		}

		echo $this->_format($array, $view);

		exit($this->exitCode);
	}

	/**
	 * _view
	 *
	 * internal low lever view parser
	 *
	 * @param string $_mvc_view_name
	 * @param mixed array
	 * @return string
	 */
	protected function _view(string $_mvc_view_name, array $_mvc_view_data = []): string
	{
		/* what file are we looking for? */
		$_mvc_view_file = FS::resolve($_mvc_view_name);

		/* extract out view data and make it in scope */
		extract($_mvc_view_data);

		/* start output cache */
		ob_start();

		/* load in view (which now has access to the in scope view data */
		require $_mvc_view_file;

		/* capture cache and return */
		return ob_get_clean();
	}
} /* end class */
