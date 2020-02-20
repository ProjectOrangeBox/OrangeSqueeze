<?php

namespace projectorangebox\collector;

interface collectorInterface
{

	public function __call($key, $arguments): collectorInterface;
	public function __toString();
	public function add(string $key, $context): collectorInterface;
	public function collect($keys = null);
	public function has($keys = null): bool;
}
