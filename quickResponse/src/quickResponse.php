<?php

namespace projectorangebox\quickResponse;

use FS;
use Exception;

class quickResponse implements quickResponseInterface
{
	protected $contentType;
	protected $view;
	protected $exitStatus = 0;
	protected $statusCode = 500;
	protected $viewFolder = '';

	public function __construct(array $config)
	{
		$this->viewFolder = '/' . trim($config['view prefix'], '/') . '/';
		$this->type($config['type']);
		$this->views = $config['views'];
	}

	public function header(int $statusCode, string $type): quickResponseInterface
	{
		return $this->code($statusCode)->type($type);
	}

	public function type(string $type): quickResponseInterface
	{
		$map = [
			'ajax' => 'application/json',
			'json' => 'application/json',
			'html' => 'text/html',
		];

		if (!isset($map[$type])) {
			throw new Exception('Unknown Content Type.');
		}

		$this->contentType = $map[$type];

		return $this;
	}

	public function code(int $statusCode): quickResponseInterface
	{
		$this->statusCode = abs($statusCode);

		if ($this->statusCode < 100) {
			$this->exitStatus = $this->statusCode + 9;
			$this->statusCode = 500;
		} else {
			$this->exitStatus = 1;
		}

		if ($this->view == null) {
			$this->view = $this->statusCode;
		}

		return $this;
	}

	public function view(string $view): quickResponseInterface
	{
		$this->view = $view;

		return $this;
	}

	public function format(array $array, string $view = null): string
	{
		return $this->_view($this->findView($view), $array);
	}

	public function display(array $array, string $view = null): void
	{
		$format = $this->_view($this->findView($view), $array);

		if ($this->contentType) {
			header("Content-type: " . $this->contentType . '; charset=UTF-8');
		}

		if ($this->statusCode > 0) {
			http_response_code($this->statusCode);
		}

		echo $format;

		exit($this->exitStatus);
	}

	protected function findView(string $view = null): string
	{
		$view = trim($this->viewFolder . ($view ?? $this->view), '/');

		if (!\array_key_exists($view, $this->views)) {
			throw new Exception('Format File Not Found.');
		}

		return $this->views[$view];
	}

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
