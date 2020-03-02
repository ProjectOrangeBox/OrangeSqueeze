<?php

namespace projectorangebox\viewresponse;

use InvalidArgumentException;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;
use projectorangebox\response\ResponseInterface;
use projectorangebox\view\ViewInterface;

class ViewResponse implements ViewResponseInterface
{
	/* default server content type */
	protected $contentType = 'text/html';

	/* default exit code */
	protected $exitCode = 0;

	/* default server status code */
	protected $statusCode = 200;

	/* prefix when trying to load a format/view */
	protected $formatFilePrefix = '';

	/* convert simple "type" into server content type response */
	protected $typeMap = [
		'ajax' => 'application/json',
		'json' => 'application/json',
		'html' => 'text/html',
		'cli' => 'text/html',
	];

	/* default charset */
	protected $charset = 'UTF-8';

	protected $response = false;

	protected $viewService;
	protected $responseService;

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

		/* override auto set */
		$this->exitCode = $config['exit code'] ?? $this->exitCode;
		$this->contentType = $config['content type'] ?? $this->contentType;

		$this->viewService = $config['viewService'];

		if (!($this->viewService instanceof ViewInterface)) {
			throw new IncorrectInterfaceException('ViewInterface');
		}

		$this->responseService = $config['responseService'];

		if (!($this->responseService instanceof ResponseInterface)) {
			throw new IncorrectInterfaceException('ResponseInterface');
		}
	}

	public function response(int $statusCode = null, string $type = null, string $charset = 'UTF-8'): ViewResponseInterface
	{
		$this->response = true;

		if ($statusCode) {
			$this->code($statusCode);
		}

		if ($type) {
			$this->type($type, $charset);
		}

		return $this;
	}

	public function view(array $array = [], string $view = null): string
	{
		$view = trim($this->formatFilePrefix . ($view ?? $this->statusCode), '/');

		if (!$this->viewService->php->exists($view)) {
			throw new ViewNotFoundException($view);
		}

		$formatted = $this->viewService->php->parse($view, $array);

		if ($this->response) {
			if ($this->contentType) {
				$this->responseService->header("Content-type: " . $this->contentType . '; charset=UTF-8');
			}

			if ($this->statusCode > 0) {
				$this->responseService->respondsCode($this->statusCode);
			}

			$this->responseService->display($formatted, $this->exitCode);
		}

		return $formatted;
	}

	/* protected */

	protected function type(string $type, string $charset = 'UTF-8'): void
	{
		if (!isset($this->typeMap[$type])) {
			throw new InvalidArgumentException('Unknown Content Type.');
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
} /* end class */
