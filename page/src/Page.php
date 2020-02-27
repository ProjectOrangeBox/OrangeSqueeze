<?php

namespace projectorangebox\page;

use Exception;
use Pear;

class Page implements PageInterface
{
	const PRIORITY_LOWEST = 10;
	const PRIORITY_LOW = 20;
	const PRIORITY_NORMAL = 50;
	const PRIORITY_HIGH = 80;
	const PRIORITY_HIGHEST = 90;

	protected $variables = [];
	protected $defaultView = '';
	protected $config;
	protected $responseService;
	protected $dataService;
	protected $pageVariablePrefix = '';
	protected $extending = false;
	protected $views = [];

	public function __construct(array $config)
	{
		$this->config = &$config;

		$this->responseService = $config['responseService'];
		$this->dataService = $config['dataService'];
		$this->views = $config['views'];

		$this->pageVariablePrefix = $config['page_prefix'] ?? 'page_';

		if ($config['page min']) {
			define('PAGEMIN', $config['page min']);
		}

		if (isset($config['default view'])) {
			$this->setDefaultView($config['default view']);
		}

		$page_configs = $config[$this->pageVariablePrefix];

		if (is_array($page_configs)) {
			foreach ($page_configs as $method => $parameters) {
				if (method_exists($this, $method)) {
					if (is_array($parameters)) {
						foreach ($parameters as $p) {
							call_user_func([$this, $method], $p);
						}
					} else {
						call_user_func([$this, $method], $parameters);
					}
				}
			}
		}

		/* global namespace static class pear:: */
		require __DIR__ . '/Pear.php';

		/* "inject" plugins */
		pear::_construct($config['plugins']);

		\log_message('info', 'Page Class Initialized');
	}

	/* set by router */
	public function setDefaultView(string $view = ''): Page
	{
		$this->defaultView = $view;

		return $this;
	}

	public function render(string $view = null, array $data = null): Page
	{
		\log_message('debug', 'page::render::' . $view);

		$view = ($view) ?? $this->defaultView;

		if ($view == null) {
			throw new \Exception('No View provided for ' . __METHOD__ . '.');
		}

		/* this is going to be the "main" section */
		$viewContent = $this->view($view, $data);

		if ($this->extending) {
			$viewContent = $this->view($this->extending);
		}

		/* append to the output responds */
		$this->responseService->append($viewContent);

		return $this;
	}

	public function view(string $view_file = null, array $data = null, $return = true)
	{
		$data = (is_array($data)) ? array_merge($this->dataService->all(), $data) : $this->dataService->all();

		if (!isset($this->views[$view_file])) {
			throw new Exception('View "' . $view_file . '" Not Found.');
		}

		$view_file = $this->views[$view_file];

		$buffer = $this->_view($view_file, $data);

		if (is_string($return)) {
			$this->data($return, $buffer);
		}

		return ($return === true) ? $buffer : $this;
	}

	protected function _view(string $__path, array $__data = []): string
	{
		extract($__data, EXTR_PREFIX_INVALID, '_');

		ob_start();

		$__returned = include \FS::resolve($__path);

		/* if nothing returned than 1 is returned */
		if ($__returned === 1) {
			$__returned = null;
		}

		$__output = ob_get_clean();

		ob_end_clean();

		return ($__returned !== null) ? $__returned : $__output;
	}

	public function data($name, $value = null): Page
	{
		if (is_array($name)) {
			foreach ($name as $index => $value) {
				$this->dataService->$index = $value;
			}
		} else {
			$this->dataService->$name = $value;
		}

		return $this;
	}

	public function extend(string $template = null): Page
	{
		if ($this->extending) {
			throw new \Exception('You are already extending "' . $this->extending . '" therefore we cannot extend "' . $template . '".');
		}

		$this->extending = $template;

		return $this;
	}

	public function linkHtml(string $file): string
	{
		return $this->ary2element('link', array_merge($this->config['link_attributes'], ['href' => $file]));
	}

	public function scriptHtml(string $file): string
	{
		return $this->ary2element('script', array_merge($this->config['script_attributes'], ['src' => $file]));
	}

	public function ary2element(string $element, array $attributes, string $content = ''): string
	{
		return (in_array($element, ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'])) ?
			'<' . $element . $this->stringifyAttributes($attributes) . '/>' :
			'<' . $element . $this->stringifyAttributes($attributes) . '>' . $content . '</' . $element . '>';
	}

	public function stringifyAttributes($attributes, $js = FALSE): string
	{
		$atts = NULL;

		if (empty($attributes)) {
			return $atts;
		}

		if (is_string($attributes)) {
			return ' ' . $attributes;
		}

		$attributes = (array) $attributes;

		foreach ($attributes as $key => $val) {
			$atts .= ($js) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';
		}

		return rtrim($atts, ',');
	}

	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		if (is_array($attr)) {
			extract($attr);
		}

		return $this->add('meta', '<meta ' . $attr . '="' . $name . '"' . (($content) ? ' content="' . $content . '"' : '') . '>' . PHP_EOL, $priority);
	}

	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		return $this->add('script', $script . PHP_EOL, $priority);
	}

	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		return $this->add('domready', $script . PHP_EOL, $priority);
	}

	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		return $this->add('title', $title, $priority);
	}

	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		return $this->add('style', $style . PHP_EOL, $priority);
	}

	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->js($f, $priority);
			}
			return $this;
		}

		return $this->add('js', $this->scriptHtml($file) . PHP_EOL, $priority);
	}

	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->css($f, $priority);
			}
			return $this;
		}

		return $this->add('css', $this->linkHtml($file) . PHP_EOL, $priority);
	}

	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): Page
	{
		if ($raw) {
			$value = 'var ' . $key . '=' . $value . ';';
		} else {
			$value = ((is_scalar($value)) ? 'var ' . $key . '="' . str_replace('"', '\"', $value) . '";' : 'var ' . $key . '=' . json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ';');
		}

		return $this->add('jsVariables', $value, $priority);
	}

	public function jsVariables(array $array): Page
	{
		foreach ($array as $k => $v) {
			$this->jsVariable($k, $v);
		}

		return $this;
	}

	public function bodyClass($class, int $priority = PAGE::PRIORITY_NORMAL): Page
	{
		return (is_array($class)) ? $this->_bodyClass($class, $priority) : $this->_bodyClass(explode(' ', $class), $priority);
	}

	public function add(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): Page
	{
		$key = md5($value);

		if (!isset($this->variables[$name][3][$key]) || !$prevent_duplicates) {
			$this->variables[$name][0] = !isset($this->variables[$name]); /* sorted */
			$this->variables[$name][1][] = (int) $priority; /* unix priority */
			$this->variables[$name][2][] = $value; /* actual html content (string) */
			$this->variables[$name][3][$key] = true; /* prevent duplicates */
		}

		return $this;
	}

	public function __call(string $name, array $arguments = [])
	{
		if ($name == 'var') {
			return $this->_var($arguments[0]);
		}

		throw new \Exception('Page Method "' . $name . '" unsupported.');
	}

	public function _var(string $name): string
	{
		$html = $this->dataService->$name;

		/* if it's empty than maybe is it a page variable? */
		if (empty($html)) {
			$get = $this->pageVariablePrefix . $name;

			$html = $this->dataService->$get;
		}

		/* does this variable key exist */
		if (isset($this->variables[$name])) {
			/* has it already been sorted */
			if (!$this->variables[$name][0]) {
				/* no we must sort it */
				array_multisort($this->variables[$name][1], SORT_DESC, SORT_NUMERIC, $this->variables[$name][2]);

				/* mark it as sorted */
				$this->variables[$name][0] = true;
			}

			foreach ($this->variables[$name][2] as $append) {
				$html .= $append;
			}
		}

		return trim($html);
	}

	protected function _bodyClass(array $class, int $priority): Page
	{
		foreach ($class as $c) {
			$this->add('body_class', ' ' . strtolower(trim($c)), $priority);
		}

		return $this;
	}
} /* end class */
