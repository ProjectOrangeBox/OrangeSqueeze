<?php

namespace projectorangebox\page;

interface PageInterface
{
	public function __construct(array &$config);
	public function setDefaultView(string $defaultView): PageInterface;
	public function render(string $view = null, array $data = null): PageInterface;
	public function view(string $viewFile = null, array $data = null, $return = true);
	public function extend(string $template = null): PageInterface;

	/* data trait */
	public function var(string $name, $value): PageInterface;
	public function vars(array $array): PageInterface;
	public function getVar(string $name); /* mixed */
	public function getVars(): array;
	public function clearVars(): PageInterface;

	/* elements trait */
	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): PageInterface;
	public function jsVariables(array $array): PageInterface;
	public function bodyClass($class, int $priority = PAGE::PRIORITY_NORMAL): PageInterface;
	public function add(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): PageInterface;

	/* formatters trait */
	public function linkHtml(string $file): string;
	public function scriptHtml(string $file): string;
	public function ary2element(string $element, array $attributes, string $content = ''): string;
	public function stringifyAttributes($attributes, $js = FALSE): string;
}
