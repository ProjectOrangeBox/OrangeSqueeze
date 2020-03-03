<?php

namespace projectorangebox\dispatcher;

use projectorangebox\container\ContainerInterface;

interface DispatcherInterface
{
	public function __construct(array &$config);
	public function dispatch(): void;
}
