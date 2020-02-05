<?php

namespace projectorangebox\dispatcher;

use Exception;
use projectorangebox\container\ContainerInterface;

class Dispatcher implements DispatcherInterface
{
	protected $container;
	protected $hasMiddleware;
	protected $request;
	protected $response;
	protected $router;
	protected $middleware;

	public function __construct(ContainerInterface $container)
	{
		/* This is injected into the controller constructor */
		$this->container = $container;

		$this->router = $container->router;
		$this->request = $container->request;
		$this->response = $container->response;

		/* test once */
		if ($this->hasMiddleware = $container->has('middleware')) {
			$this->middleware = $container->middleware;
		}
	}

	public function dispatch(): void
	{
		\log_message('info', __CLASS__);

		$httpMethod = $this->request->requestMethod();
		$uri = $this->request->uri();
		$matched = $this->router->handle($uri, $httpMethod);
		$captured = $this->router->captured();
		$segments = explode('/', $uri);

		/* middleware input */
		if ($this->hasMiddleware) {
			/* passed by reference */
			$this->middleware->request($this->container);
		}

		list($namespaceClass, $method, $params) = $matched;

		if (!\class_exists($namespaceClass, true)) {
			/* throw low level error */
			throw new Exception('Class "' . $namespaceClass . '" not found.');
		}

		$controller = new $namespaceClass($this->container);

		if (!\method_exists($controller, $method)) {
			/* throw low level error */
			throw new Exception('Method "' . $method . '" on "' . $namespaceClass . '" not found.');
		}

		$params = '/' . trim($params, '/') . '/';

		foreach ($segments as $index => $value) {
			$params = str_replace('$Seg' . ($index + 1), $value, $params);
		}

		foreach ($captured as $key => $value) {
			$params = str_replace('$' . $key, $value, $params);
		}

		ob_start();

		/* call the controller method */
		\call_user_func_array([$controller, $method], explode('/', trim($params, '/')));

		$output = ob_get_contents();

		ob_end_clean();

		$this->response->append($output);

		/* middleware output */
		if ($this->hasMiddleware) {
			/* passed by reference */
			$this->middleware->response($this->container);
		}

		$this->response->display();
	}
} /* end class */
