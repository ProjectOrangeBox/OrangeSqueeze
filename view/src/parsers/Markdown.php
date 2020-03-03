<?php

namespace projectorangebox\view\parsers;

use FS;
use Michelf\Markdown as MichelfMarkdown;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;
use projectorangebox\view\parsers\ParserInterface;

class Markdown implements ParserInterface
{
	protected $config = [];
	protected $views = [];
	protected $delimiters = ['{{', '}}'];
	protected $cacheFolder = '';
	protected $forceCompile = true;

	public function __construct(array &$config)
	{
		$this->views = $config['views'] ?? [];
		$this->delimiters = $config['delimiters'] ?? ['{{', '}}'];
		$this->cacheFolder = $config['cache folder'] ?? '/var/cache/markdown';
		$this->forceCompile = $config['forceCompile'] ?? DEBUG;

		FS::mkdir($this->cacheFolder);
	}

	public function set_delimiters(/* string|array */$l = '{{', string $r = '}}'): ParserInterface
	{
		/* set delimiters */
		$this->delimiters = (is_array($l)) ? $l : [$l, $r];

		/* chain-able */
		return $this;
	}

	public function add(string $name, string $value): ParserInterface
	{
		$this->views[strtolower($name)] = $value;

		return $this;
	}

	public function exists(string $name): bool
	{
		$name = strtolower(trim($name, '/'));

		log_message('info', 'Find ' . $name);

		return isset($this->views[$name]);
	}

	public function parse(string $view, array $data = []): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		return $this->_parse(FS::file_get_contents($this->views[strtolower(trim($view, '/'))], true), $data);
	}

	public function parse_string(string $string, array $data = []): string
	{
		return $this->_parse($string, $data);
	}

	protected function _parse(string $template, array $data): string
	{
		return $this->merge(FS::file_get_contents($this->compileFile($template)), $data);
	}

	protected function merge(string $string, array $parameters): string
	{
		$left_delimiter = preg_quote($this->delimiters[0]);
		$right_delimiter = preg_quote($this->delimiters[1]);

		$replacer = function ($match) use ($parameters) {
			return isset($parameters[$match[1]]) ? $parameters[$match[1]] : $match[0];
		};

		return preg_replace_callback('/' . $left_delimiter . '\s*(.+?)\s*' . $right_delimiter . '/', $replacer, $string);
	}

	protected function compileFile(string $template): string
	{
		$compiledFile = $this->cacheFolder . '/' . md5($template) . '.php';

		if ($this->forceCompile || !FS::file_exists($compiledFile)) {
			FS::file_put_contents($compiledFile, MichelfMarkdown::defaultTransform($template));
		}

		return $compiledFile;
	}
} /* end class */
