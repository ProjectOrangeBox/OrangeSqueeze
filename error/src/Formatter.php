<?php

namespace projectorangebox\error;

use Exception;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\error\Errors;
use projectorangebox\view\ViewInterface;
use projectorangebox\error\ErrorsInterface;
use projectorangebox\error\FormatterInterface;
use projectorangebox\response\ResponseInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Formatter implements FormatterInterface
{
	protected $errorsService;

	protected $viewPrefix = 'errors/';
	protected $contentType = [];

	protected $exitStatus = 0;
	protected $statusCode = 0;
	protected $viewfile = null;

	protected $viewService;
	protected $responseService;

	public function __construct(array $config, ErrorsInterface $errorsService)
	{
		$this->errorsService = $errorsService;

		$this->viewPrefix = $config['view prefix'] ?? $this->viewPrefix; /* view key prefix */
		$this->contentType = $config['content type'] ?? $this->contentType; /* output content type */

		$this->viewService = $config['viewService'];

		if (!($this->viewService instanceof ViewInterface)) {
			throw new IncorrectInterfaceException('ViewInterface');
		}

		$this->responseService = $config['responseService'];

		if (!($this->responseService instanceof ResponseInterface)) {
			throw new IncorrectInterfaceException('ResponseInterface');
		}

		$this->status('default');
	}

	public function status($statusCode): FormatterInterface
	{
		$this->statusCode = abs($statusCode);

		if ($this->statusCode < 100) {
			$this->exitStatus = $this->statusCode + 9;
			$this->statusCode = 500;
		} else {
			$this->exitStatus = 1;
		}

		if ($this->viewfile == null) {
			$this->viewfile = $this->statusCode;
		}

		return $this;
	}

	public function view(string $viewfile): FormatterInterface
	{
		$this->viewfile = $viewfile;

		return $this;
	}

	public function contentType(string $mimeType, string $charset = 'utf-8'): FormatterInterface
	{
		$this->contentType[0] = $mimeType;
		$this->contentType[1] = $charset;

		return $this;
	}

	public function viewPrefix(string $viewPrefix): FormatterInterface
	{
		$this->viewPrefix = $viewPrefix;

		return $this;
	}

	public function addView(string $view, string $path): FormatterInterface
	{
		$this->viewService->php->add($this->viewPrefix . $view, $path);

		return $this;
	}

	public function onError($groups = null): void
	{
		if ($this->errorsService->has($groups)) {
			$this->error($groups);
		}
	}

	public function error($groups = null): void
	{
		if (count($this->contentType) == 2) {
			$this->responseService->contentType($this->contentType[0], $this->contentType[1]);
		}

		if ($this->statusCode > 0) {
			$this->responseService->respondsCode($this->statusCode);
		}

		$viewPath = $this->viewPrefix . $this->viewfile;

		if (!$this->viewService->php->exists($viewPath)) {
			throw new ViewNotFoundException($viewPath);
		}

		$this->responseService->display($this->viewService->php->parse($viewPath, ['errors' => $this->errorsService->get($groups)]), $this->exitStatus);
	}
} /* end class */
