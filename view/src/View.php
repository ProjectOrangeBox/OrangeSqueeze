<?php

namespace projectorangebox\view;

use Exception;
use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class View implements ViewInterface
{
	protected $knownParsers;
	protected $defaultParser = 'php';

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$default = $config['default'] ?? 'php';

		foreach ($config['parsers'] as $name => $parserClass) {
			var_dump($name, $parserClass);

			$this->knownParsers[$name] = $parserClass;

			if (!($this->knownParsers[$name] instanceof ParserInterface)) {
				throw new IncorrectInterfaceException('ParserInterface');
			}
		}

		if (!\array_key_exists($default, $config['parsers'])) {
			throw new Exception($default . ' parser not found.');
		}

		\log_message('info', 'Default view parser is ' . $default);

		$this->defaultParser = $this->knownParsers[$default];
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
		return $this->defaultParser->parse($view, $data);
	}

	public function parse_string(string $string, array $data = []): string
	{
		return $this->defaultParser->parse_string($string, $data);
	}
} /* end class */
