<?php

namespace projectorangebox\collector;

use ArgumentCountError;

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

		return $this->add($key, $arguments[0]);
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
	public function add(string $key, $context): collectorInterface
	{
		$hashKey = md5($key . '@' . json_encode($context));

		if (!array_key_exists($hashKey, $this->duplicateKeys)) {
			$this->_add($key, $context);
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

		if ($keys === null) {
			$collected = $this->collection;
		} elseif (is_string($keys)) {
			$keys = explode(',', $keys);
		}

		if (is_array($keys)) {
			foreach (array_keys($this->collection) as $key) {
				if (in_array($key, $keys)) {
					$collected[$key] = $this->collection[$key];
				}
			}
		}

		return (count($collected) == 1) ? array_shift($collected) : $collected;
	}

	public function has($keys = null): bool
	{
		if ($keys === null) {
			$keys = array_keys($this->collection);
		} elseif (is_string($keys)) {
			$keys = explode(',', $keys);
		}

		$has = false;

		if (is_array($keys)) {
			foreach (array_keys($this->collection) as $key) {
				if (isset($this->collection[$key])) {
					$has = true;

					break;
				}
			}
		}

		return $has;
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
} /* end class */
