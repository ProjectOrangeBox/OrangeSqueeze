<?php

namespace projectorangebox\data;

use Adbar\Dot;

interface DataInterface
{

	public function __construct(array &$config);
	public function set($notations, $value = null, bool $persist = false): DataInterface;
	public function all(): array;
	public function dot(): Dot;
	public function clear(string $notation): DataInterface;
	public function count(string $notation): int;
	public function delete(string $notation): DataInterface;
	public function get(string $notation, $default = null);
	public function has($notations): bool;
	public function isEmpty($notations = null): bool;
	public function pull(string $notation, $default = null);
	public function push(string $notation, $value): DataInterface;
}
