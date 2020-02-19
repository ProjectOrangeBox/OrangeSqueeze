<?php

namespace projectorangebox\error;

use Exception;
use projectorangebox\view\ViewInterface;
use projectorangebox\response\ResponseInterface;

class Errors implements ErrorsInterface
{
	const ROOT = '@root';

	protected $duplicates = []; /* prevent duplicates */
	protected $errors = []; /* main array */

	protected $group = 'errors';
	protected $viewPrefix = 'errors/';
	protected $view = 'array';
	protected $contentType = [];
	protected $viewVariable = 'errors';

	protected $viewService;
	protected $responseService;
	protected $exitStatus = 0;
	protected $statusCode = 0;

	public function __construct(array $config, ViewInterface $viewService, ResponseInterface $responseService)
	{
		$this->group = $this->config['group'] ?? $this->group; /* default group */
		$this->viewPrefix = $config['view prefix'] ?? $this->viewPrefix; /* view key prefix */
		$this->view = 'default'; /* current view */
		$this->viewVariable = $config['view variable'] ?? $this->viewVariable; /* view page variable */
		$this->contentType = $config['content type'] ?? $this->contentType; /* output content type */

		$this->viewService = $viewService;
		$this->responseService = $responseService;
	}

	public function setGroup(string $group): ErrorsInterface
	{
		$this->group = $group;

		return $this;
	}

	public function getGroup(): string
	{
		return $this->group;
	}

	public function setViewVariable(string $variable): ErrorsInterface
	{
		$this->viewVariable = $variable;

		return $this;
	}

	public function getViewVariable(): string
	{
		return $this->viewVariable;
	}

	public function viewPrefix(string $viewPrefix): ErrorsInterface
	{
		$this->viewPrefix = $viewPrefix;

		return $this;
	}

	public function addView(string $view, string $value): ErrorsInterface
	{
		$this->viewService->php->add($this->viewPrefix . $view, $value);

		return $this;
	}

	public function contentType(string $mimeType, string $charset = 'utf-8'): ErrorsInterface
	{
		$this->contentType[0] = $mimeType;
		$this->contentType[1] = $charset;

		return $this;
	}

	public function add(string $index, $value, string $group = null): ErrorsInterface
	{
		$group = ($group) ?? $this->group;

		$dupKey = $group . $index . $value;

		if (!isset($this->duplicates[$dupKey])) {
			if ($group == Errors::ROOT) {
				$this->errors[$index] = $value;
			} else {
				$this->errors[$group][$index] = $value;
			}

			$this->duplicates[$dupKey] = true;
		}

		return $this;
	}

	public function getGroups($groups = null): array
	{
		return $this->extract_groups($groups, true);
	}

	public function has($groups = null): bool
	{
		foreach ($this->extract_groups($groups, true) as $key) {
			if (is_array($this->errors[$key])) {
				if (count($this->errors[$key])) {
					return true;
				}
			}
		}

		return false;
	}

	public function clear($groups = null): ErrorsInterface
	{
		foreach ($this->extract_groups($groups, true) as $key) {
			$this->errors[$key] = [];
		}

		return $this;
	}

	public function getView($groups = null, string $view) /* mixed */
	{
		return $this->view($view)->get($groups);
	}

	public function view(string $view): ErrorsInterface
	{
		$this->view = $view;

		return $this;
	}

	public function get($groups = null) /* mixed */
	{
		$response  = $this->extract_groups($groups);

		if ($this->view !== 'array') {
			if (!$this->viewService->php->exists($this->viewPrefix . $this->view)) {
				throw new Exception(__METHOD__ . ' Unknown View "' . $this->viewPrefix . $this->view . '".');
			}

			$response = $this->viewService->php->parse($this->viewPrefix . $this->view, [$this->viewVariable => $response]);
		}

		return $response;
	}

	public function displayOnError($groups = null, int $statusCode = 500, string $view = null): void
	{
		if ($this->has($groups)) {
			$this->displayError($groups, $statusCode, $view);
		}
	}

	public function displayError($groups = null, int $statusCode = 500, string $view = null): void
	{
		$this->setStatus($statusCode)->view($view ?? $this->statusCode);

		$this->_display($this->get($groups));
	}

	public function display(string $title, string $body, int $statusCode = 500, string $view = null): void
	{
		$this->setStatus($statusCode)->view($view ?? $this->statusCode)->add('title', $title, Errors::ROOT)->add('body', $body, Errors::ROOT);

		$this->_display($this->get());
	}

	protected function _display(string $output): void
	{
		if (count($this->contentType) == 2) {
			$this->responseService->contentType($this->contentType[0], $this->contentType[1]);
		}

		if ($this->statusCode > 0) {
			$this->responseService->respondsCode($this->statusCode);
		}

		$this->responseService->display($output, $this->exitStatus);
	}

	protected function setStatus($statusCode): ErrorsInterface
	{
		$this->statusCode = abs($statusCode);

		if ($this->statusCode < 100) {
			$this->exitStatus = $this->statusCode + 9;
			$this->statusCode = 500;
		} else {
			$this->exitStatus = 1;
		}

		return $this;
	}

	protected function extract_groups($groups = null, $keys = false): array
	{
		/* if null return entire error collection */
		if ($groups === null) {
			$errors = $this->errors;
		} elseif (is_array($groups)) {
			foreach ($groups as $key) {
				$errors[$key] = $this->errors[$key] ?? [];
			}
		} else {
			$errors[$groups] = $this->errors[$groups] ?? [];
		}

		return ($keys) ? array_keys($errors) : $errors;
	}
} /* end class */
