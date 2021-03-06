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

interface HtmlInterface
{

	public function __construct(array &$config);
	public function meta(string $attr, string $name = null, string $content = null, int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function script(string $script, int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function domready(string $script, int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function title(string $title = ''): HtmlInterface;
	public function style(string $style, int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function js($file = '', int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function css($file = '', int $priority = Html::PRIORITY_NORMAL): HtmlInterface;
	public function jsVariable(string $key, $value, int $priority = Html::PRIORITY_NORMAL, bool $raw = false): HtmlInterface;
	public function jsVariables(array $array): HtmlInterface;
	public function bodyClass($class): HtmlInterface;
	public function linkHtml(string $file): string;
	public function scriptHtml(string $file): string;
	public function attributes2element(string $element, array $attributes, string $content = ''): string;
	public function stringifyAttributes($attributes, $js = FALSE): string;
	public function set(string $name, string $value, int $priority = Html::PRIORITY_NORMAL, int $options = 0): HtmlInterface;
	public function get(string $name): string;
}
