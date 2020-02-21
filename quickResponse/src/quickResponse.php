<?php

namespace projectorangebox\quickResponse;

use FS;
use Exception;

class quickResponse implements quickResponseInterface
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

	/**
	 * header
	 *
	 * Change server response type and server response code and exit code
	 *
	 * @param int $statusCode
	 * @param string $type
	 * @param mixed string
	 * @return quickResponseInterface
	 */
	public function header(int $statusCode, string $type, string $charset = 'UTF-8'): quickResponseInterface
	{
		return $this->code($statusCode)->type($type, $charset);
	}

	/**
	 * type
	 *
	 * Change server response type
	 *
	 * @param string $type
	 * @param mixed string
	 * @return quickResponseInterface
	 */
	public function type(string $type, string $charset = 'UTF-8'): quickResponseInterface
	{
		if (!isset($this->typeMap[$type])) {
			throw new Exception('Unknown Content Type.');
		}

		$this->contentType = $this->typeMap[$type];
		$this->charset = $charset;

		return $this;
	}

	/**
	 * code
	 *
	 * Change server response code and exit code
	 *
	 * @param int $statusCode
	 * @return quickResponseInterface
	 */
	public function code(int $statusCode): quickResponseInterface
	{
		$this->statusCode = abs($statusCode);

		if ($this->statusCode < 100) {
			$this->exitCode = $this->statusCode + 9;
			$this->statusCode = 500;
		} else {
			$this->exitCode = 1;
		}

		return $this;
	}

	/**
	 * format
	 *
	 * format and return
	 *
	 * @param mixed array
	 * @param mixed string
	 * @return string
	 */
	public function format(array $array = [], string $view = null): string
	{
		$view = trim($this->formatFilePrefix . ($view ?? $this->statusCode), '/');

		if (!\array_key_exists($view, $this->formatFiles)) {
			throw new Exception('Format File "' . $view . '" Not Found.');
		}

		return $this->_view($this->formatFiles[$view], $array);
	}

	/**
	 * display
	 *
	 * Display & Die
	 *
	 * @param mixed array
	 * @param mixed string
	 * @return void
	 */
	public function display(array $array = [], string $view = null): void
	{
		if ($this->contentType) {
			header("Content-type: " . $this->contentType . '; charset=UTF-8');
		}

		if ($this->statusCode > 0) {
			http_response_code($this->statusCode);
		}

		echo $this->format($array, $view);

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
