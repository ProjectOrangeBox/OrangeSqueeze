<?php

namespace projectorangebox\collector;

use ArgumentCountError;
use Exception;
use projectorangebox\session\SessionInterface;
use projectorangebox\collector\collectorInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class collector implements collectorInterface
{
	/**
	 * $collection
	 *
	 * @var array
	 */
	protected $collection = [];

	/**
	 * $duplicateKeys
	 *
	 * Track entries to determine if it's a duplicate
	 *
	 * @var array
	 */
	protected $duplicateKeys = [];

	/**
	 * $keyCount
	 *
	 * Track the count in each group
	 *
	 * @var array
	 */
	protected $keyCount = [];

	protected $sessionService;

	protected $flashKey = 'flashdata::';

	protected $persistenceSupported = false;

	public function __construct(array $config)
	{
		if (isset($config['session service'])) {
			$this->sessionService = $config['session service'];

			if (!($this->sessionService instanceof SessionInterface)) {
				throw new IncorrectInterfaceException('SessionInterface');
			}

			$this->persistenceSupported = true;

			$this->insertPersistenceContext();
		}
	}

	/**
	 * __call
	 *
	 * $class->http('Can't open page');
	 *
	 * @param mixed $key
	 * @param mixed $arguments
	 * @return void
	 */
	public function __call($key, $arguments): collectorInterface
	{
		if (!isset($arguments[0])) {
			throw new ArgumentCountError('Context Required.');
		}

		$persist = $arguments[1] ?? false;

		return $this->add($key, $arguments[0], (bool) $persist);
	}

	/**
	 * __toString
	 *
	 * convert to json for string output
	 *
	 * @return void
	 */
	public function __toString()
	{
		return json_encode($this->collect());
	}

	public function __debugInfo()
	{
		return [
			'collection' => $this->collection,
			'keyCount' => $this->keyCount,
			'duplicateKeys' => array_keys($this->duplicateKeys),
			'flashKey' => $this->flashKey,
			'persistenceSupported' => $this->persistenceSupported,
		];
	}

	/**
	 * add
	 *
	 * @param string $key
	 * @param string $message
	 * @param mixed array
	 * @return void
	 */
	public function add(string $key, $context, bool $persist = false): collectorInterface
	{
		$hashKey = md5($key . '@' . json_encode($context));

		if (!array_key_exists($hashKey, $this->duplicateKeys)) {
			$this->_add($key, $context);

			if ($persist) {
				$this->addPersistent($key);
			}
		}

		$this->duplicateKeys[$hashKey] = true;

		return $this;
	}

	/**
	 * collect
	 *
	 * @param mixed $keys
	 * @return void
	 */
	public function collect($keys = null)
	{
		$collected = [];

		foreach ($this->keys2Array($keys) as $key) {
			if (isset($this->collection[$key])) {
				$collected[$key] = $this->collection[$key];
			}
		}

		return (count($collected) == 1) ? array_shift($collected) : $collected;
	}

	public function has($keys = null): bool
	{
		$has = false;

		foreach ($this->keys2Array($keys) as $key) {
			if (isset($this->collection[$key])) {
				$has = true;

				break;
			}
		}

		return $has;
	}

	public function clear($keys = null): collectorInterface
	{
		foreach ($this->keys2Array($keys) as $key) {
			unset($this->collection[$key]);
			unset($this->keyCount[$key]);
			if ($this->persistenceSupported) {
				$this->sessionService->delete($this->flashKey . $key);
			}
		}

		return $this;
	}

	protected function keys2Array($keys = null)
	{
		if ($keys === null) {
			$keys = array_keys($this->collection);
		} elseif (is_string($keys)) {
			$keys = explode(',', $keys);
		}

		return (array) $keys;
	}

	protected function _add(string $key, $context): void
	{
		if (!isset($this->keyCount[$key])) {
			$this->collection[$key] = $context;
		} elseif ($this->keyCount[$key] == 1) {
			$current = $this->collection[$key];

			$this->collection[$key] = [];
			$this->collection[$key][] = $current;
			$this->collection[$key][] = $context;
		} else {
			$this->collection[$key][] = $context;
		}

		@$this->keyCount[$key]++;
	}

	protected function addPersistent(string $key): void
	{
		if ($this->persistenceSupported) {
			$this->sessionService->set($this->flashKey . $key, $this->collection[$key]);
		} else {
			throw new Exception('Session not supplied to collector so presistence is not supported.');
		}
	}

	protected function insertPersistenceContext(): void
	{
		if ($this->persistenceSupported) {
			$flashKeyStrLen = strlen($this->flashKey);

			foreach ($this->sessionService as $key => $context) {
				if (substr($key, 0, $flashKeyStrLen) == $this->flashKey) {
					/* add it to the collection */
					$this->add(substr($key, $flashKeyStrLen), $context);

					/* now delete the persistent record */
					$this->sessionService->delete($key);
				}
			}
		}
	}
} /* end class */
