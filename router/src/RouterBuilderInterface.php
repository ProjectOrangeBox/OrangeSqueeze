<?php

namespace projectorangebox\router;

use projectorangebox\cache\CacheInterface;

interface RouterBuilderInterface
{
	public function __construct(array $config, string $key, CacheInterface $cache = null);
	public function build(): array;
}
