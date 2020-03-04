<?php

namespace projectorangebox\view;

use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;
use projectorangebox\common\exceptions\mvc\ParserForExtentionNotFoundException;

class View implements ViewInterface
{
	protected $knownParsers;
	protected $config = [];
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

	public function parse(string $view, array $data = []): string
	{
		$output = null;

		/* search for the view - search order based on the parser config order */
		foreach ($this->parserOrder as $name) {
			if ($this->knownParsers[$name]->exists($view)) {
				$output = $this->knownParsers[$name]->parse($view, $data);
			}
		}

		if ($output === null) {
			throw new ViewNotFoundException($view);
		}

		return $output;
	}

	public function parse_string(string $string, array $data = [], string $ext = null): string
	{
		$ext = $ext ?? array_key_first($this->parserOrder);

		return $this->knownParsers[$ext]->parse_string($string, $data);
	}

	/******************
	 * add data
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
