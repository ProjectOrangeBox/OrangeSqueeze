<?php

namespace projectorangebox\view\parsers;

use FS;
use projectorangebox\view\parsers\ParserInterface;

class Php implements ParserInterface
{
	protected $config = [];
	protected $views = [];
	protected $cacheFolder = '';
	protected $forceCompile = true;

	public function __construct(array &$config)
	{
		$this->views = $config['views'] ?? [];
		$this->cacheFolder = $config['cache folder'] ?? '/var/cache/markdown';
		$this->forceCompile = $config['forceCompile'] ?? DEBUG;

		FS::mkdir($this->cacheFolder);
	}

	public function add(string $name, string $value): ParserInterface
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
		return ($this->exists($view)) ? $this->_parse($this->views[$view], $data) : '';
	}

	public function parse_string(string $string, array $data = []): string
	{
		$path = $this->cacheFolder . '/' . md5($string);

		FS::file_put_contents($path, $string, FILE_APPEND | LOCK_EX);

		return $this->_parse($path, $data);
	}

	/* protected */

	protected function _parse(string $__path, array $__data = []): string
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
} /* end class */
