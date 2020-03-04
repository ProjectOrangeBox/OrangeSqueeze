<?php

namespace projectorangebox\view\parsers\page\pear;

use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\view\ViewInterface;

abstract class PearAbstract
{
	protected $pageClass;

	public function __construct(ParserInterface $pageClass)
	{
		$this->pageClass = $pageClass;
	}

	public function render()
	{
	}
}
