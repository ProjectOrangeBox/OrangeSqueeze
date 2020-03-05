<?php

namespace projectorangebox\view\parsers;

use FS;
use Michelf\Markdown as MichelfMarkdown;
use projectorangebox\view\parsers\ParserAbstract;
use projectorangebox\view\parsers\ParserInterface;

class Markdown extends ParserAbstract implements ParserInterface
{
	protected $merge = false;

	public function __construct(array &$config)
	{
		parent::__construct($config);

		$this->merge = $config['merge'] ?? $this->merge;
	}

	public function _parse(string $__path, array $__data = []): string
	{
		$compiledFile = $this->cacheFolder . '/' . md5($__path) . '.php';

		if ($this->forceCompile || !FS::file_exists($compiledFile)) {
			FS::file_put_contents($compiledFile, MichelfMarkdown::defaultTransform(FS::file_get_contents($__path)));
		} else {
			$output = FS::file_get_contents($compiledFile);
		}

		return ($this->merge) ? $this->merge($output, $__data, $this->delimiters) : $output;
	}
} /* end class */
