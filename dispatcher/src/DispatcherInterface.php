<?php

namespace projectorangebox\dispatcher;

use projectorangebox\container\ContainerInterface;

interface DispatcherInterface
{
	public function __construct(ContainerInterface $container);
	public function dispatch(): void;
}
