<?php

namespace projectorangebox\view\parsers;

use FS;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;

abstract class ParserAbstract implements ParserInterface
{
	protected $config = [];
	protected $views = [];
	protected $cacheFolder = '';
	protected $forceCompile = true;
	protected $delimiters = ['{', '}'];

	public function __construct(array &$config)
	{
		$this->config = &$config;

		$this->views = $config['views'] ?? [];
		$this->cacheFolder = $config['cache folder'] ?? '/var/tmp';
		$this->forceCompile = $config['forceCompile'] ?? DEBUG;
		$this->delimiters = $config['delimiters'] ?? ['{', '}'];

		FS::mkdir($this->cacheFolder);
	}

	/* string|array */
	public function setDelimiters($l = '{{', string $r = '}}'): ParserInterface
	{
		/* set delimiters */
		$this->delimiters = (is_array($l)) ? $l : [$l, $r];

		return $this;
	}

	public function addView(string $name, string $value): ParserInterface
	{
		$this->views[$name] = $value;

		return $this;
	}

	public function exists(string $view): bool
	{
		return \array_key_exists($view, $this->views);
	}

	public function parse(string $view, array $data = []): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		return $this->_parse($this->views[$view], $data);
	}

	public function parseString(string $string, array $data = []): string
	{
		$path = $this->cacheFolder . '/' . md5($string);

		if ($this->forceCompile || !FS::file_exists($path)) {
			FS::file_put_contents($path, $string);
		}

		return $this->_parse($path, $data);
	}

	/**
	 * _parse
	 *
	 * Parse based on file
	 *
	 * @param string $__path
	 * @param mixed array
	 * @return void
	 */
	public function _parse(string $__path, array $__data = []): string
	{
		/* Replaces elements from passed arrays into the first array */
		$__data = array_replace(service('view')->getVars(), $__data);

		/* Import variables into the current symbol table from an array */
		extract($__data, EXTR_PREFIX_INVALID, '_');

		/* Turn on output buffering */
		ob_start();

		$__returned = include FS::resolve($__path);

		/* if nothing returned than 1 is returned */
		if ($__returned === 1) {
			$__returned = null;
		}

		/* Flush the output buffer, return it as a string and turn off output buffering */
		$__output = ob_get_clean();

		return ($__returned !== null) ? $__returned : $__output;
	}

	/* simple mail merge */
	public function merge(string $string, array $parameters, array $delimiters): string
	{
		$left_delimiter = preg_quote($delimiters[0]);
		$right_delimiter = preg_quote($delimiters[1]);

		$replacer = function ($match) use ($parameters) {
			return isset($parameters[$match[1]]) ? $parameters[$match[1]] : $match[0];
		};

		return preg_replace_callback('/' . $left_delimiter . '\s*(.+?)\s*' . $right_delimiter . '/', $replacer, $string);
	}
} /* end class */
