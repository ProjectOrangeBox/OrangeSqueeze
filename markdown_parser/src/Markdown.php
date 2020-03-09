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

namespace projectorangebox\markdown_parser;

use FS;
use Michelf\Markdown as MichelfMarkdown;
use projectorangebox\view\ParserInterface;
use projectorangebox\view\ParserAbstract;

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
