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

namespace projectorangebox\page_parser;

use projectorangebox\view\ParserInterface;
use projectorangebox\view\ParserAbstract;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;

class Page extends ParserAbstract implements ParserInterface
{
	protected $defaultView = '';
	protected $extending = [];

	public function __construct(array &$config)
	{
		parent::__construct($config);

		$this->defaultView = $config['default view'] ?? '';
	}

	public function parse(string $view, array $data = []): string
	{
		/* if they send in '@' for "auto" use the default view */
		$view = ($view == '@') ? $this->defaultView : $view;

		$this->extending[] = $view;

		while ($view = array_pop($this->extending)) {
			$viewContent = $this->parseSingle($view, $data);
		}

		return $viewContent;
	}

	public function parseSingle(string $view, array $data): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		return $this->_parse($this->views[$view], $data);
	}

	/* set by router */
	public function setDefaultView(string $defaultView): ParserInterface
	{
		$this->defaultView = $defaultView;

		return $this;
	}

	public function extend(string $extend): ParserInterface
	{
		$this->extending[] = $extend;

		return $this;
	}
} /* end class */
