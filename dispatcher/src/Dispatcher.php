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

namespace projectorangebox\dispatcher;

use Exception;
use projectorangebox\router\RouterInterface;
use projectorangebox\request\RequestInterface;
use projectorangebox\response\ResponseInterface;
use projectorangebox\container\ContainerInterface;
use projectorangebox\middleware\MiddlewareInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Dispatcher implements DispatcherInterface
{
	protected $container;
	protected $requestService;
	protected $responseService;
	protected $routerService;
	protected $hasMiddlewareHandlerService;
	protected $middlewareHandlerService;

	/* get AFTER construct */
	protected $captured = [];
	protected $segments = [];

	public function __construct(array &$config)
	{
		/* This is injected into the controller constructor */
		$this->container = &$config['container'];

		if (!($this->container instanceof ContainerInterface)) {
			throw new IncorrectInterfaceException('ContainerInterface');
		}

		$this->routerService = &$config['routerService'];

		if (!($this->routerService instanceof RouterInterface)) {
			throw new IncorrectInterfaceException('RouterInterface');
		}

		$this->requestService = &$config['requestService'];

		if (!($this->requestService instanceof RequestInterface)) {
			throw new IncorrectInterfaceException('RequestInterface');
		}

		$this->responseService = &$config['responseService'];

		if (!($this->responseService instanceof ResponseInterface)) {
			throw new IncorrectInterfaceException('ResponseInterface');
		}

		/* test once */
		if ($this->hasMiddlewareHandlerService = $this->container->has('middleware')) {
			if ($this->container->middleware instanceof MiddlewareInterface) {
				$this->middlewareHandlerService = $this->container->middleware;
			} else {
				/* throw fatal low level error */
				throw new IncorrectInterfaceException('MiddlewareInterface');
			}
		}
	}

	public function dispatch(): void
	{
		\log_message('info', __METHOD__);

		$httpMethod = $this->requestService->requestMethod();
		$uri = $this->requestService->uri();
		$matched = $this->routerService->handle($uri, $httpMethod);

		list($namespaceClass, $method, $parameters) = $matched;

		$method = $method ?? 'index';
		$parameters = $parameters ?? '';

		if (!$namespaceClass) {
			throw new Exception('Route Service returned invalid Controller Class.');
		}

		$this->captured = $this->routerService->captured();
		$this->segments = explode('/', $uri);

		/* if response has a displayCache function call it with the Uri */
		if (\method_exists($this->responseService, 'displayCache')) {
			$this->responseService->displayCache($uri);
		}

		/* middleware input */
		if ($this->hasMiddlewareHandlerService) {
			/* passed by reference */
			$this->middlewareHandlerService->request();
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
		if ($output = call_user_func_array([$controller, $method], explode('/', trim($parameters, '/')))) {
			$this->responseService->append($output);
		}

		/* middleware output */
		if ($this->hasMiddlewareHandlerService) {
			/* passed by reference */
			$this->middlewareHandlerService->response();
		}

		$this->responseService->display();
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
