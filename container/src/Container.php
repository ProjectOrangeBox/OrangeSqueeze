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

namespace projectorangebox\container;

class Container implements ContainerInterface
{
	/**
	 * Registered Services
	 *
	 * @var array
	 */
	protected $registeredServices = [];

	/**
	 * __construct
	 *
	 * @param mixed array (optional)
	 * @return di
	 */
	public function __construct(array &$services)
	{
		foreach ($services as $serviceName => $closureSingleton) {
			/* name, closure, singleton or factory */
			$this->register($serviceName, $closureSingleton[0], ($closureSingleton[1] ?? true));
		}
	}

	/**
	 * __get
	 *
	 * see get(...)
	 *
	 * @param mixed $serviceName
	 * @return mixed
	 */
	public function __get(string $serviceName)
	{
		return $this->get($serviceName);
	}

	/**
	 * __isset
	 *
	 * see has(...)
	 *
	 * @param mixed $serviceName
	 * @return bool
	 */
	public function __isset(string $serviceName): bool
	{
		return $this->has($serviceName);
	}

	/**
	 * __set
	 *
	 * see regsiter(...)
	 *
	 * @param mixed $serviceName
	 * @param mixed $value
	 * @return void
	 */
	public function __set(string $serviceName, $reference): void
	{
		$this->registeredServices[$serviceName]['reference'] = &$reference;
	}

	/**
	 * __unset
	 *
	 * see remove(...)
	 *
	 * @param mixed $serviceName
	 * @return void
	 */
	public function __unset(string $serviceName): void
	{
		$this->remove($serviceName);
	}

	/**
	 * Get a PHP object by service name
	 *
	 * @param string $serviceName
	 * @return mixed
	 */
	public function get(string $serviceName)
	{
		/* Is this service even registered? */
		if (!isset($this->registeredServices[$serviceName])) {
			/* fatal */
			throw new \Exception('"' . $serviceName . '" service not registered.');
		}

		/* Is this a singleton or factory? */
		return ($this->registeredServices[$serviceName]['singleton']) ? self::singleton($serviceName) : self::factory($serviceName);
	}

	/**
	 * Check whether the Service been registered
	 *
	 * @param string $serviceName
	 * @return bool
	 */
	public function has(string $serviceName): bool
	{
		return isset($this->registeredServices[$serviceName]);
	}

	/**
	 * Register a new service as a singleton or factory
	 *
	 * @param string $serviceName Service Name
	 * @param closure $closure closure to call in order to instancate it.
	 * @param bool $singleton should this be a singleton or factory
	 * @return void
	 */
	public function register(string $serviceName, \closure $closure, bool $singleton = true): void
	{
		$this->registeredServices[$serviceName] = ['closure' => $closure, 'singleton' => $singleton, 'reference' => null];
	}

	/**
	 * Remove a Registered Service
	 *
	 * @param string $serviceName
	 * @return void
	 */
	public function remove(string $serviceName): void
	{
		unset($this->registeredServices[$serviceName]);
	}

	/**
	 * Get the same instance of a service
	 *
	 * @param string $serviceName
	 * @return mixed
	 */
	protected function singleton(string $serviceName)
	{
		return $this->registeredServices[$serviceName]['reference'] ?? $this->registeredServices[$serviceName]['reference'] = self::factory($serviceName);
	}

	/**
	 * Get new instance of a service
	 *
	 * @param string $serviceName
	 * @return mixed
	 */
	protected function factory(string $serviceName)
	{
		return $this->registeredServices[$serviceName]['closure']($this);
	}

	/**
	 * returns a debug array
	 *
	 * @return array
	 */
	public function debug(): array
	{
		$debug = [];

		foreach ($this->registeredServices as $key => $record) {
			$debug[$key] = ['singleton' => $record['singleton'], 'attached' => isset($this->registeredServices[$key]['reference'])];
		}

		return $debug;
	}
} /* end class */
