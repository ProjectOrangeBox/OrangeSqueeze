<?php

namespace projectorangebox\middleware;

use MiddlewareRequestInterface;
use MiddlewareResponseInterface;
use projectorangebox\container\ContainerInterface;

class Middleware implements MiddlewareInterface
{
	protected $config;
	protected $container;

	public function __construct(array $config, ContainerInterface &$container)
	{
		$this->config = $config;
		$this->container = $container;
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
			if ($middleware->$method($this->container) === false) {
				$continue = false;
			}
		}

		/* call the controller method */
		return $continue;
	}

	protected function exists(string $namedSpacedClass, string $method) /* mixed */
	{
		/* NOTE: This method has multiple exists */

		if (!\class_exists($namedSpacedClass, true)) {
			\log_message('debug', 'Class "' . $namedSpacedClass . '" not found.');

			return false; /* return #1 */
		}

		$middleware = new $namedSpacedClass($this->container);

		$mustImplement = 'Middleware' . \ucfirst($method) . 'Interface';

		/* let's make sure they implment the correct interface this will also enforce the method */
		if (!($middleware instanceof $mustImplement)) {
			\log_message('debug', 'Class "' . $namedSpacedClass . '" does not implement ' . $mustImplement . '.');

			return false; /* return #2 */
		}

		return $middleware;
	}
} /* end middleware class */
