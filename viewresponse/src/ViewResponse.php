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

namespace projectorangebox\viewresponse;

use InvalidArgumentException;
use projectorangebox\view\ViewInterface;
use projectorangebox\response\ResponseInterface;
use projectorangebox\view\exceptions\ViewNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class ViewResponse implements ViewResponseInterface
{
	/* default server content type */
	protected $contentType = 'text/html';

	/* content type */
	protected $type = '';

	/* default exit code */
	protected $exitCode = 0;

	/* default server status code */
	protected $statusCode = 200;

	/* prefix when trying to load a format/view */
	protected $viewFormat = '';

	/* convert simple "type" into server content type response */
	protected $typeMap = [
		'ajax' => 'application/json',
		'json' => 'application/json',
		'html' => 'text/html',
		'cli' => 'text/html',
	];

	/* default charset */
	protected $charset = 'UTF-8';

	/* should a responds be sent? */
	protected $response = false;

	/* view parser */
	protected $parserHandler = 'php';

	protected $viewService;
	protected $responseService;

	/**
	 * __construct
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->typeMap = $config['type map'] ?? $this->typeMap;

		$this->type(($config['type'] ?? 'html'), ($config['charset'] ?? $this->charset));
		$this->code($config['status code'] ?? $this->statusCode);

		$this->viewFormat = $config['view format'] ?? 'formats/{type}/{view}';

		/* override auto set */
		$this->exitCode = $config['exit code'] ?? $this->exitCode;
		$this->contentType = $config['content type'] ?? $this->contentType;

		$this->parserHandler = $config['parser'] ?? $this->parserHandler;

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

	public function view(array $data = [], string $view = null): string
	{
		if (!$view) {
			$view = str_replace(
				['{type}', '{status}', '{mime}', '{charset}', '{exit}'],
				[$this->type, $this->statusCode, $this->contentType, $this->charset, $this->exitCode],
				$this->viewFormat
			);
		}

		if (!$this->viewService->{$this->parserHandler}->exists($view)) {
			throw new ViewNotFoundException($view);
		}

		$formatted = $this->viewService->{$this->parserHandler}->parse($view, $data);

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

		$this->type = $type;
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
