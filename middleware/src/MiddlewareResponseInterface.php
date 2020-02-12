<?php

use projectorangebox\container\ContainerInterface;

interface MiddlewareResponseInterface
{
	public function response(ContainerInterface &$container): void;
}
