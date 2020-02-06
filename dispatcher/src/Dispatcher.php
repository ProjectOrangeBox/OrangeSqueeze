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
		$captured = $this->router->captured();
		$segments = explode('/', $uri);

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

		if (!\method_exists($controller, $method)) {
			/* throw low level error */
			throw new Exception('Method "' . $method . '" on "' . $namespaceClass . '" not found.');
		}

		/* format the parameters */
		$parameters = '/' . trim($parameters, '/') . '/';

		/* segments $Seg1, $Seg2, $Seg3... */
		foreach ($segments as $index => $value) {
			$parameters = str_replace('/$Seg' . ($index + 1) . '/', $value, $parameters);
		}

		/* regular expression captured values $cat, $dog, $orderid, $month, $date */
		foreach ($captured as $key => $value) {
			$parameters = str_replace('/$' . $key . '/', $value, $parameters);
		}

		/* Turn on output buffering */
		ob_start();

		/* call the controller method */
		\call_user_func_array([$controller, $method], explode('/', trim($parameters, '/')));


		/* capture the contents of the output buffer */
		$output = ob_get_contents();

		/* Clean the output buffer and turn off output buffering */
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
