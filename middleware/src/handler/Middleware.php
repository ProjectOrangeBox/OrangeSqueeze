<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\middleware\handler;

use Exception;
use projectorangebox\container\ContainerInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Middleware implements MiddlewareInterface
{
	protected $config;
	protected $container;

	public function __construct(array &$config)
	{
		$this->config = $config;
		$this->container = &$config['containerService'];

		if (!($this->container instanceof ContainerInterface)) {
			throw new IncorrectInterfaceException('ContainerInterface');
		}
	}

	public function request(): void
	{
		$this->loop('request');
	}

	public function response(): void
	{
		$this->loop('response');
	}

	protected function loop(string $method): void
	{
		$httpMethod = $this->container->request->requestMethod();
		if (isset($this->config[$method], $this->config[$method][$httpMethod])) {
			foreach ($this->config[$method][$httpMethod] as $regex => $namedSpacedClass) {
				if (preg_match($regex, $this->container->request->uri())) {
					if ($this->trigger($namedSpacedClass, $method) === false) {
						break; /* break out if false returned */
					}
				}
			}
		}
	}

	protected function trigger(string $namedSpacedClass, string $method): bool
	{
		$continue = true;

		if ($middleware = $this->exists($namedSpacedClass, $method)) {
			if ($middleware->$method() === false) {
				$continue = false;
			}
		}

		/* call the controller method */
		return $continue;
	}

	protected function exists(string $namedSpacedClass, string $method) /* mixed */
	{
		if (!\class_exists($namedSpacedClass, true)) {
			throw new Exception('Could not locate "' . $namedSpacedClass . '".');
		}

		$middleware = new $namedSpacedClass($this->container);

		$mustImplement = '\projectorangebox\middleware\Middleware' . \ucfirst($method) . 'Interface';

		/* let's make sure they implment the correct interface this will also enforce the method */
		if (!($middleware instanceof $mustImplement)) {
			throw new Exception('Class "' . $namedSpacedClass . '" does not implement ' . $mustImplement . '.');
		}

		return $middleware;
	}
} /* end middleware class */
