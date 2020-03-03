<?php

namespace projectorangebox\page;

use FS;
use Pear;
use projectorangebox\page\traits\DataTrait;
use projectorangebox\page\traits\ElementsTrait;
use projectorangebox\response\ResponseInterface;
use projectorangebox\common\exceptions\mvc\ParserException;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Page implements PageInterface
{
	use DataTrait;
	use ElementsTrait;

	const PRIORITY_LOWEST = 10;
	const PRIORITY_LOW = 20;
	const PRIORITY_NORMAL = 50;
	const PRIORITY_HIGH = 80;
	const PRIORITY_HIGHEST = 90;

	protected $variables = [];
	protected $defaultView = '';
	protected $dataService;
	protected $pageVariablePrefix = '';
	protected $extending = false;
	protected $views = [];
	protected $viewData = [];
	protected $link_attributes;
	protected $script_attributes;
	protected $variablesPrefix = 'add::';

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->link_attributes = $config['link attributes'] ?? ['href' => '', 'type' => 'text/css', 'rel' => 'stylesheet'];
		$this->script_attributes = $config['script attributes'] ?? ['src' => '', 'type' => 'text/javascript', 'charset' => 'utf-8'];

		/* views we know of */
		$this->views = $config['views'] ?? [];

		/* if a default view was sent in set it */
		if (isset($config['default view'])) {
			$this->setDefaultView($config['default view']);
		}

		if (\is_array($config['define'])) {
			foreach ($config['define'] as $key => $value) {
				define($key, $value);
			}
		}

		/* all page created/managed variables start with */
		$this->pageVariablePrefix = $config['page_prefix'] ?? 'page_';

		$page_configs = $config[$this->pageVariablePrefix];

		if (is_array($page_configs)) {
			foreach ($page_configs as $method => $individualCalls) {
				if (method_exists($this, $method)) {
					foreach ($individualCalls as $parameters) {
						call_user_func_array([$this, $method], (array) $parameters);
					}
				}
			}
		}

		/* global namespace static class pear:: */
		require __DIR__ . '/Pear.php';

		/* "inject" plugins */
		Pear::_construct($config['plugins']);

		\log_message('info', 'Page Class Initialized');
	}

	/* set by router */
	public function setDefaultView(string $defaultView): PageInterface
	{
		$this->defaultView = $defaultView;

		return $this;
	}

	public function render(string $view = null, array $data = null): string
	{
		\log_message('info', 'page::render::' . $view);

		$view = ($view) ?? $this->defaultView;

		if ($view == null) {
			throw new ViewNotFoundException();
		}

		/* this is going to be the "main" section */
		$viewContent = $this->view($view, $data);

		if ($this->extending) {
			$viewContent = $this->view($this->extending);
		}

		return $viewContent;
	}

	public function view(string $viewFile = null, array $data = null, $return = true)
	{
		$data = array_replace($this->viewData, (array) $data);

		if (!isset($this->views[$viewFile])) {
			throw new ViewNotFoundException($viewFile);
		}

		$viewFile = $this->views[$viewFile];

		$buffer = $this->_view($viewFile, $data);

		if (is_string($return)) {
			$this->viewData[$return] = $buffer;
		}

		return ($return === true) ? $buffer : $this;
	}

	public function extend(string $template = null): PageInterface
	{
		if ($this->extending) {
			throw new ParserException('You are already extending "' . $this->extending . '" therefore we cannot extend "' . $template . '".');
		}

		$this->extending = $template;

		return $this;
	}

	/* protected */

	protected function _view(string $__path, array $__data = []): string
	{
		extract($__data, EXTR_PREFIX_INVALID, '_');

		ob_start();

		$__returned = include FS::resolve($__path);

		/* if nothing returned than 1 is returned */
		if ($__returned === 1) {
			$__returned = null;
		}

		$__output = ob_get_clean();

		ob_end_clean();

		return ($__returned !== null) ? $__returned : $__output;
	}
} /* end class */
