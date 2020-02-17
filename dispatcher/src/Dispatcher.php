<?php

namespace projectorangebox\dispatcher;

use Exception;
use projectorangebox\container\ContainerInterface;
use projectorangebox\middleware\MiddlewareInterface;

class Dispatcher implements DispatcherInterface
{
	protected $container;
	protected $hasMiddleware;
	protected $request;
	protected $response;
	protected $router;
	protected $middleware;

	/* get AFTER construct */
	protected $captured = [];
	protected $segments = [];

	public function __construct(ContainerInterface $container)
	{
		/* This is injected into the controller constructor */
		$this->container = $container;

		$this->router = $container->router;
		$this->request = $container->request;
		$this->response = $container->response;

		/* test once */
		if ($this->hasMiddleware = $container->has('middleware')) {
			if ($container->middleware instanceof MiddlewareInterface) {
				$this->middleware = $container->middleware;
			} else {
				/* throw fatal low level error */
				throw new Exception('Middleware attached to the container is not a instance of MiddlewareInterface.');
			}
		}
	}

	public function dispatch(): void
	{
		\log_message('info', __CLASS__);

		$httpMethod = $this->request->requestMethod();
		$uri = $this->request->uri();
		$matched = $this->router->handle($uri, $httpMethod);

		$this->captured = $this->router->captured();
		$this->segments = explode('/', $uri);

		/* middleware input */
		if ($this->hasMiddleware) {
			/* passed by reference */
			$this->middleware->request($this->container);
		}

		if (is_array($matched)) {
			list($namespaceClass, $method, $parameters) = $matched;

			$method = $method ?? 'index';
			$parameters = $parameters ?? '';
		} else {
			$namespaceClass = $matched;

			$method = 'index';
			$parameters = '';
		}

		if (!\class_exists($namespaceClass, true)) {
			/* throw fatal low level error */
			throw new Exception('Class "' . $namespaceClass . '" not found.');
		}

		/* create a instance of the controller class and inject the container */
		$controller = new $namespaceClass($this->container);

		$method = $this->replace($method);

		if (!\method_exists($controller, $method)) {
			/* throw low level error */
			throw new Exception('Method "' . $method . '" on "' . $namespaceClass . '" not found.');
		}

		/* format the parameters */
		$parameters = $this->replace($parameters);

		/* call the controller method */
		$output = \call_user_func_array([$controller, $method], explode('/', trim($parameters, '/')));

		if ($output) {
			$this->response->append((string) $output);
		}

		/* middleware output */
		if ($this->hasMiddleware) {
			/* passed by reference */
			$this->middleware->response($this->container);
		}

		$this->response->display();
	}

	protected function replace(string $string): string
	{
		/* segments $Seg1, $Seg2, $Seg3... */
		foreach ($this->segments as $index => $value) {
			$string = str_replace('$Seg' . ($index + 1), $value, $string);
		}

		/* regular expression captured values $cat, $dog, $orderid, $month, $date */
		foreach ($this->captured as $key => $value) {
			$string = str_replace('$' . $key, $value, $string);
		}

		return $string;
	}
} /* end class */
