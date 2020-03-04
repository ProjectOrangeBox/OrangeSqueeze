<?php

namespace projectorangebox\view\parsers\page;

use Pear;
use projectorangebox\view\parsers\ParserAbstract;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\mvc\ParserException;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;

class Page extends ParserAbstract implements ParserInterface
{
	const PRIORITY_LOWEST = 10;
	const PRIORITY_LOW = 20;
	const PRIORITY_NORMAL = 50;
	const PRIORITY_HIGH = 80;
	const PRIORITY_HIGHEST = 90;

	protected $defaultView = '';
	protected $views = [];
	protected $pageVar = [];
	protected $blockVar = [];
	protected $link_attributes;
	protected $script_attributes;
	protected $cacheFolder = '';
	protected $extending = '';

	public function __construct(array &$config)
	{
		parent::__construct($config);

		$this->link_attributes = $config['link attributes'] ?? ['href' => '', 'type' => 'text/css', 'rel' => 'stylesheet'];
		$this->script_attributes = $config['script attributes'] ?? ['src' => '', 'type' => 'text/javascript', 'charset' => 'utf-8'];

		/* if a default view was sent in set it */
		if (isset($config['default view'])) {
			$this->setDefaultView($config['default view']);
		}

		if (\is_array($config['define'])) {
			foreach ($config['define'] as $key => $value) {
				define($key, $value);
			}
		}

		$page_configs = $config['elements'];

		if (is_array($page_configs)) {
			foreach ($page_configs as $method => $individualCalls) {
				if (method_exists($this, $method)) {
					foreach ($individualCalls as $parameters) {
						call_user_func_array([$this, $method], (array) $parameters);
					}
				}
			}
		}

		/* global namespaced static class pear:: so we must manually load it */
		require __DIR__ . '/pear/Pear.php';

		/* "inject" plugins */
		Pear::_construct($config['plugins'], $this);

		\log_message('info', 'Page Class Initialized');
	}

	public function render(string $view = null, array $data = []): string
	{
		$view = ($view) ?? $this->defaultView;

		if ($view == null) {
			throw new ViewNotFoundException();
		}

		return trim($this->parse($view, $data));
	}

	public function parse(string $view, array $data = []): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		$this->extending = $view;

		while ($this->extending) {
			$view = $this->extending;

			$this->extending = false;

			$viewContent = $this->_parse($this->views[$view], $data);
		}

		return $viewContent;
	}

	public function findView(string $name)
	{
		return $this->views[$name] ?? false;
	}

	/* set by router */
	public function setDefaultView(string $defaultView): ParserInterface
	{
		$this->defaultView = $defaultView;

		return $this;
	}

	public function extend(string $template = null): ParserInterface
	{
		if ($this->extending) {
			throw new ParserException('You are already extending "' . $this->extending . '" therefore we cannot extend "' . $template . '".');
		}

		$this->extending = $template;

		return $this;
	}

	/******************
	 * add elements
	 ******************/

	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($attr)) {
			extract($attr);
		}

		return $this->pageVar('meta', '<meta ' . $attr . '="' . $name . '"' . (($content) ? ' content="' . $content . '"' : '') . '>' . PHP_EOL, $priority);
	}

	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->pageVar('script', $script . PHP_EOL, $priority);
	}

	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->pageVar('domready', $script . PHP_EOL, $priority);
	}

	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->pageVar('title', $title, $priority);
	}

	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->pageVar('style', $style . PHP_EOL, $priority);
	}

	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->js($f, $priority);
			}
			return $this;
		}

		return $this->pageVar('js', $this->scriptHtml($file) . PHP_EOL, $priority);
	}

	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->css($f, $priority);
			}
			return $this;
		}

		return $this->pageVar('css', $this->linkHtml($file) . PHP_EOL, $priority);
	}

	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): ParserInterface
	{
		if ($raw) {
			$value = 'var ' . $key . '=' . $value . ';';
		} else {
			$value = ((is_scalar($value)) ? 'var ' . $key . '="' . str_replace('"', '\"', $value) . '";' : 'var ' . $key . '=' . json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ';');
		}

		return $this->pageVar('jsVariables', $value, $priority);
	}

	public function jsVariables(array $array): ParserInterface
	{
		foreach ($array as $k => $v) {
			$this->jsVariable($k, $v);
		}

		return $this;
	}

	public function bodyClass($class, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return (is_array($class)) ? $this->_bodyClass($class, $priority) : $this->_bodyClass(explode(' ', $class), $priority);
	}

	public function linkHtml(string $file): string
	{
		return $this->ary2element('link', array_merge($this->link_attributes, ['href' => $file]));
	}

	public function scriptHtml(string $file): string
	{
		return $this->ary2element('script', array_merge($this->script_attributes, ['src' => $file]));
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

	protected function _bodyClass(array $class, int $priority): ParserInterface
	{
		foreach ($class as $c) {
			$this->pageVar('body_class', ' ' . strtolower(trim($c)), $priority);
		}

		return $this;
	}

	/* page var collection or other */

	public function pageVar(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): ParserInterface
	{
		$key = md5($value);

		if (!isset($this->pageVar[$name][3][$key]) || !$prevent_duplicates) {
			$this->pageVar[$name][0] = !isset($this->pageVar[$name]); /* sorted */
			$this->pageVar[$name][1][] = (int) $priority; /* unix priority */
			$this->pageVar[$name][2][] = $value; /* actual html content (string) */
			$this->pageVar[$name][3][$key] = true; /* prevent duplicates */
		}

		return $this;
	}

	public function getPageVar(string $name) /* mixed */
	{
		$response = '';

		if (isset($this->pageVar[$name])) {
			/* has it already been sorted */
			if (!$this->pageVar[$name][0]) {
				/* no we must sort it */
				array_multisort($this->pageVar[$name][1], SORT_DESC, SORT_NUMERIC, $this->pageVar[$name][2]);

				/* mark it as sorted */
				$this->pageVar[$name][0] = true;
			}

			foreach ($this->pageVar[$name][2] as $append) {
				$response .= $append;
			}
		}

		return $response;
	}

	/* page blocks */

	public function setBlock(string $name, $value): ParserInterface
	{
		$this->blockVar[$name] = $value;

		return $this;
	}

	public function getBlock(string $name)
	{
		return $this->blockVar[$name] ?? null;
	}
} /* end class */
