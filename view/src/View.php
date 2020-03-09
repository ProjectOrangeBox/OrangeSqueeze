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

namespace projectorangebox\view;

use projectorangebox\view\ViewInterface;
use projectorangebox\view\ParserInterface;
use projectorangebox\view\exceptions\ViewNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;
use projectorangebox\view\exceptions\ParserForExtentionNotFoundException;

class View implements ViewInterface
{
	protected $config = [];
	protected $knownParsers;
	protected $parserOrder = [];
	protected $viewData = [];

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->config = &$config;

		foreach ($config['parsers'] as $name => $parserClass) {
			$this->knownParsers[$name] = $parserClass;

			if (!($this->knownParsers[$name] instanceof ParserInterface)) {
				throw new IncorrectInterfaceException('ParserInterface');
			}
		}

		$this->parserOrder = $config['parser order'] ?? \array_keys($config['parsers']);
	}

	public function __get(string $name): ParserInterface
	{
		if (!\array_key_exists($name, $this->knownParsers)) {
			throw new ParserForExtentionNotFoundException($name);
		}

		return $this->knownParsers[$name];
	}

	public function __set(string $extension, ParserInterface $parser)
	{
		$this->knownParsers[$extension] = $parser;
	}

	public function build(string $view): string
	{
		$ext = $ext ?? $this->parserOrder[0];

		$this->knownParser($ext);

		$output = null;

		if ($this->knownParsers[$ext]->exists($view)) {
			$output = $this->knownParsers[$ext]->parse($view, $this->viewData);
		} else {
			/* search for the view - search order based on the parser config order */
			foreach ($this->parserOrder as $name) {
				if ($this->knownParsers[$name]->exists($view)) {
					$output = $this->knownParsers[$name]->parse($view, $this->viewData);
				}
			}
		}

		if ($output === null) {
			throw new ViewNotFoundException($view);
		}

		return $output;
	}

	protected function knownParser(string $ext): void
	{
		if (!isset($this->knownParsers[$ext])) {
			throw new ParserForExtentionNotFoundException($ext);
		}
	}

	/******************
	 * view data
	 ******************/

	public function var(string $name, $value): ViewInterface
	{
		$this->viewData[$name] = $value;

		return $this;
	}

	public function vars(array $array): ViewInterface
	{
		foreach ($array as $key => $value) {
			$this->viewData[$key] = $value;
		}

		return $this;
	}

	public function getVar(string $name) /* mixed */
	{
		return $this->viewData[$name] ?? null;
	}

	public function getVars(): array
	{
		return $this->viewData;
	}

	public function clearVars(): ViewInterface
	{
		$this->viewData = [];

		return $this;
	}
} /* end class */
