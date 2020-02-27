<?php

namespace projectorangebox\page\traits;

use projectorangebox\page\Page;
use projectorangebox\page\PageInterface;

trait ElementsTrait
{
	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		if (is_array($attr)) {
			extract($attr);
		}

		return $this->add('meta', '<meta ' . $attr . '="' . $name . '"' . (($content) ? ' content="' . $content . '"' : '') . '>' . PHP_EOL, $priority);
	}

	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		return $this->add('script', $script . PHP_EOL, $priority);
	}

	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		return $this->add('domready', $script . PHP_EOL, $priority);
	}

	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		return $this->add('title', $title, $priority);
	}

	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		return $this->add('style', $style . PHP_EOL, $priority);
	}

	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->js($f, $priority);
			}
			return $this;
		}

		return $this->add('js', $this->scriptHtml($file) . PHP_EOL, $priority);
	}

	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		if (is_array($file)) {
			foreach ($file as $f) {
				$this->css($f, $priority);
			}
			return $this;
		}

		return $this->add('css', $this->linkHtml($file) . PHP_EOL, $priority);
	}

	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): PageInterface
	{
		if ($raw) {
			$value = 'var ' . $key . '=' . $value . ';';
		} else {
			$value = ((is_scalar($value)) ? 'var ' . $key . '="' . str_replace('"', '\"', $value) . '";' : 'var ' . $key . '=' . json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . ';');
		}

		return $this->add('jsVariables', $value, $priority);
	}

	public function jsVariables(array $array): PageInterface
	{
		foreach ($array as $k => $v) {
			$this->jsVariable($k, $v);
		}

		return $this;
	}

	public function bodyClass($class, int $priority = PAGE::PRIORITY_NORMAL): PageInterface
	{
		return (is_array($class)) ? $this->_bodyClass($class, $priority) : $this->_bodyClass(explode(' ', $class), $priority);
	}

	public function add(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): PageInterface
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

	protected function _bodyClass(array $class, int $priority): PageInterface
	{
		foreach ($class as $c) {
			$this->add('body_class', ' ' . strtolower(trim($c)), $priority);
		}

		return $this;
	}
}
