<?php

namespace projectorangebox\view;

use FS;
use Exception;
use projectorangebox\common\exceptions\mvc\ParserForExtentionNotFoundException;
use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
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

	static public function view(string $__path, array $__data = []): string
	{
		extract($__data, EXTR_PREFIX_INVALID, '_');

		ob_start();

		$__returned = include FS::resolve($__path);

		/* if nothing returned than 1 is returned */
		if ($__returned === 1) {
			$__returned = null;
		}

		$__output = ob_get_clean();

		ob_end_clean();

		return ($__returned !== null) ? $__returned : $__output;
	}

	static public function merge(string $string, array $parameters, array $delimiters): string
	{
		$leftDelimiter = preg_quote($delimiters[0]);
		$rightDelimiter = preg_quote($delimiters[1]);

		$replacer = function ($match) use ($parameters) {
			return isset($parameters[$match[1]]) ? $parameters[$match[1]] : $match[0];
		};

		return preg_replace_callback('/' . $leftDelimiter . '\s*(.+?)\s*' . $rightDelimiter . '/', $replacer, $string);
	}
} /* end class */
