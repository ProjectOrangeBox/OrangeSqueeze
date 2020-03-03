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
		\log_message('info', __METHOD__ . ' get ' . $name);

		return $this->container->$name;
	}
}
