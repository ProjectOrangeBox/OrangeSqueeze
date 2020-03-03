<?php

namespace projectorangebox\view;

use Exception;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class View implements ViewInterface
{
	protected $knownParsers;
	protected $config = [];

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
	}

	public function __get(string $name): ParserInterface
	{
		return $this->knownParsers[$name] ?? null;
	}

	public function __set(string $extension, ParserInterface $parser)
	{
		$this->knownParsers[$extension] = $parser;
	}

	public function parse(string $view, array $data = []): string
	{
		$output = null;

		/* search for the view - search order based on the parser config order */
		foreach (array_keys($this->config['parsers']) as $name) {
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
		$ext = $ext ?? array_key_first($this->knownParsers);

		return $this->knownParsers[$ext]->parse_string($string, $data);
	}
} /* end class */
