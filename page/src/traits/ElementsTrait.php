<?php

namespace projectorangebox\page\traits;

use projectorangebox\page\Page;
use projectorangebox\view\parsers\ParserInterface;

trait ElementsTrait
{
	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($attr)) {
			extract($attr);
		}

		return $this->add('meta', '<meta ' . $attr . '="' . $name . '"' . (($content) ? ' content="' . $content . '"' : '') . '>' . PHP_EOL, $priority);
	}

	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->add('script', $script . PHP_EOL, $priority);
	}

	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->add('domready', $script . PHP_EOL, $priority);
	}

	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->add('title', $title, $priority);
	}

	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		return $this->add('style', $style . PHP_EOL, $priority);
	}

	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->js($f, $priority);
			}
			return $this;
		}

		return $this->add('js', $this->scriptHtml($file) . PHP_EOL, $priority);
	}

	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->css($f, $priority);
			}
			return $this;
		}

		return $this->add('css', $this->linkHtml($file) . PHP_EOL, $priority);
	}

	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): ParserInterface
	{
		if ($raw) {
			$value = 'var ' . $key . '=' . $value . ';';
		} else {
			$value = ((is_scalar($value)) ? 'var ' . $key . '="' . str_replace('"', '\"', $value) . '";' : 'var ' . $key . '=' . json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ';');
		}

		return $this->add('jsVariables', $value, $priority);
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

	public function add(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): ParserInterface
	{
		$name = $this->variablesPrefix . $name;
		$key = md5($value);

		if (!isset($this->variables[$name][3][$key]) || !$prevent_duplicates) {
			$this->variables[$name][0] = !isset($this->variables[$name]); /* sorted */
			$this->variables[$name][1][] = (int) $priority; /* unix priority */
			$this->variables[$name][2][] = $value; /* actual html content (string) */
			$this->variables[$name][3][$key] = true; /* prevent duplicates */
		}

		return $this;
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
			$this->add('body_class', ' ' . strtolower(trim($c)), $priority);
		}

		return $this;
	}
}
