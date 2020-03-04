<?php

namespace projectorangebox\page;

interface ParserInterface
{
	public function __construct(array &$config);
	public function setDefaultView(string $defaultView): ParserInterface;
	public function render(string $view = null, array $data = null): string;
	public function view(string $viewFile = null, array $data = null, $return = true);
	public function extend(string $template = null): ParserInterface;

	/* data trait */
	public function var(string $name, $value): ParserInterface;
	public function vars(array $array): ParserInterface;
	public function getVar(string $name); /* mixed */
	public function getVars(): array;
	public function clearVars(): ParserInterface;

	/* elements trait */
	public function meta($attr, string $name = null, string $content = null, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function script(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function domready(string $script, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function title(string $title = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function style(string $style, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function js($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function css($file = '', int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function jsVariable(string $key, $value, int $priority = PAGE::PRIORITY_NORMAL, bool $raw = false): ParserInterface;
	public function jsVariables(array $array): ParserInterface;
	public function bodyClass($class, int $priority = PAGE::PRIORITY_NORMAL): ParserInterface;
	public function add(string $name, string $value, int $priority = PAGE::PRIORITY_NORMAL, bool $prevent_duplicates = true): ParserInterface;

	/* formatters trait */
	public function linkHtml(string $file): string;
	public function scriptHtml(string $file): string;
	public function ary2element(string $element, array $attributes, string $content = ''): string;
	public function stringifyAttributes($attributes, $js = FALSE): string;
}
