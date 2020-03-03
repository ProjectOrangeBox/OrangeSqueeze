<?php

namespace projectorangebox\app;

use projectorangebox\container\ContainerInterface;

interface AppInterface
{
	static public function container(): ContainerInterface;

	public function __construct(array &$config);
	public function dispatch(): void;
}
