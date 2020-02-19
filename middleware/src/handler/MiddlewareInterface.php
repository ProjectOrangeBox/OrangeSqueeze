<?php

namespace projectorangebox\middleware\handler;

use projectorangebox\container\ContainerInterface;

interface MiddlewareInterface
{
	public function __construct(array $config, ContainerInterface &$container);
	public function request(): void;
	public function response(): void;
}
