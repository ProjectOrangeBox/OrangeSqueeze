<?php

use projectorangebox\container\ContainerInterface;

interface MiddlewareRequestInterface
{
	public function request(ContainerInterface &$container): void;
}
