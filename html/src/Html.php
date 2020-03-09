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

namespace projectorangebox\html;

class Html implements HtmlInterface
{
	const PRIORITY_LOWEST = 10;
	const PRIORITY_LOW = 20;
	const PRIORITY_NORMAL = 50;
	const PRIORITY_HIGH = 80;
	const PRIORITY_HIGHEST = 90;

	const REPLACE = 1;
	const ALLOWDUPS = 2;

	const IDX_SORTED = 0;
	const IDX_PRIORITY = 1;
	const IDX_VALUE = 2;

	protected $config = [];
	protected $variables = [];
	protected $variableCaches = [];
	protected $duplicates = [];
	protected $emptyElements = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
	protected $linkAttributes = ['href' => '', 'type' => 'text/css', 'rel' => 'stylesheet'];
	protected $scriptAttributes = ['src' => '', 'type' => 'text/javascript', 'charset' => 'utf-8'];
	protected $bodyClasses = '';

	public function __construct(array &$config)
	{
		$this->config = $config;

		$this->linkAttributes = $config['link attributes'] ?? $this->linkAttributes;
		$this->scriptAttributes = $config['script attributes'] ?? $this->scriptAttributes;

		if (is_array($config['elements'])) {
			foreach ($config['elements'] as $method => $individualCalls) {
				if (method_exists($this, $method)) {
					foreach ($individualCalls as $parameters) {
						call_user_func_array([$this, $method], (array) $parameters);
					}
				}
			}
		}
	}

	public function meta(string $attr, string $name = null, string $content = null, int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		return $this->set('meta', '<meta ' . $attr . '="' . $name . '"' . (($content) ? ' content="' . $content . '"' : '') . '>' . PHP_EOL, $priority);
	}

	public function script(string $script, int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		return $this->set('script', $script . PHP_EOL, $priority);
	}

	public function domready(string $script, int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		return $this->set('domready', $script . PHP_EOL, $priority);
	}

	public function title(string $title = ''): HtmlInterface
	{
		return $this->set('title', $title, Html::REPLACE);
	}

	public function style(string $style, int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		return $this->set('style', $style . PHP_EOL, $priority);
	}

	public function js($file = '', int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->js($f, $priority);
			}
			return $this;
		}

		return $this->set('js', $this->scriptHtml($file) . PHP_EOL, $priority);
	}

	public function css($file = '', int $priority = Html::PRIORITY_NORMAL): HtmlInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->css($f, $priority);
			}
			return $this;
		}

		return $this->set('css', $this->linkHtml($file) . PHP_EOL, $priority);
	}

	public function jsVariable(string $key, $value, int $priority = Html::PRIORITY_NORMAL, bool $raw = false): HtmlInterface
	{
		if ($raw) {
			$value = 'var ' . $key . '=' . $value . ';';
		} else {
			$value = ((is_scalar($value)) ? 'var ' . $key . '="' . str_replace('"', '\"', $value) . '";' : 'var ' . $key . '=' . json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ';');
		}

		return $this->set('jsVariables', $value, $priority);
	}

	public function jsVariables(array $array): HtmlInterface
	{
		foreach ($array as $k => $v) {
			$this->jsVariable($k, $v);
		}

		return $this;
	}

	public function bodyClass($class): HtmlInterface
	{
		$classes = (is_array($class)) ? $class : explode(' ', $class);

		$this->bodyClasses = \array_replace($this->bodyClasses, \array_combine($classes, $classes));

		$this->set('body_class', implode(' ', $this->bodyClasses, Html::REPLACE));

		return $this;
	}

	public function linkHtml(string $file): string
	{
		return $this->ary2element('link', array_merge($this->linkAttributes, ['href' => $file]));
	}

	public function scriptHtml(string $file): string
	{
		return $this->ary2element('script', array_merge($this->scriptAttributes, ['src' => $file]));
	}

	public function ary2element(string $element, array $attributes, string $content = ''): string
	{
		return (in_array($element, $this->emptyElements)) ? '<' . $element . $this->stringifyAttributes($attributes) . '/>' : '<' . $element . $this->stringifyAttributes($attributes) . '>' . $content . '</' . $element . '>';
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


	/* page var collection or other */

	/* options
	 * 1 - prevent duplicates
	 * 2 - replace
	 */
	public function set(string $name, string $value, int $priority = Html::PRIORITY_NORMAL, int $options = 0): HtmlInterface
	{
		$key = md5($name . $value);

		/* if replace then remove what in there now */
		if ($options & Html::REPLACE) {
			$this->variables[$name] = [];
		}

		if (!isset($this->duplicates[$key]) || $options & Html::ALLOWDUPS) {
			$this->variables[$name][Html::IDX_SORTED] = !isset($this->variables[$name]); /* sorted */
			$this->variables[$name][Html::IDX_PRIORITY][] = (int) $priority; /* unix priority */
			$this->variables[$name][Html::IDX_VALUE][] = $value; /* actual html content (string) */

			$this->duplicates[$key] = true; /* prevent duplicates */
		}

		return $this;
	}

	public function get(string $name): string
	{
		$response = '';

		if (isset($this->variables[$name])) {
			/* has it already been sorted */
			if (!$this->variables[$name][Html::IDX_SORTED]) {
				/* no we must sort it */
				array_multisort($this->variables[$name][Html::IDX_PRIORITY], SORT_DESC, SORT_NUMERIC, $this->variables[$name][Html::IDX_VALUE]);

				/* build the responds */
				foreach ($this->variables[$name][Html::IDX_VALUE] as $append) {
					$response .= $append;
				}

				$this->variableCaches[$name] = $response;

				/* mark it as sorted */
				$this->variables[$name][Html::IDX_SORTED] = true;
			} else {
				$response = $this->variableCaches[$name] ?? '';
			}
		}

		return $response;
	}
}
