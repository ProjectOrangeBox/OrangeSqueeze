<?php

namespace projectorangebox\middleware;

use projectorangebox\container\ContainerInterface;

abstract class Middleware
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function __get(string $name) /* mixed */
	{
		return $this->container->$name;
	}
}
