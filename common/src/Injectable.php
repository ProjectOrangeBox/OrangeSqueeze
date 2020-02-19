<?php

namespace projectorangebox\common;

use projectorangebox\container\ContainerInterface;

class Injectable
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
