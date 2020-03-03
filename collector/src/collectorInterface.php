<?php

namespace projectorangebox\collector;

interface collectorInterface
{

	public function __construct(array &$config);
	public function __call($key, $arguments): collectorInterface;
	public function __toString();
	public function add(string $key, $context, bool $persist = false): collectorInterface;
	public function collect($keys = null);
	public function has($keys = null): bool;
	public function clear($keys = null): collectorInterface;
}
