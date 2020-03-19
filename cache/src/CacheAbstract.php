<?php

namespace projectorangebox\cache;

abstract class CacheAbstract
{
	protected $cacheTTL = 0;

	public function ttl(int $cacheTTL = null, bool $useWindow = true): int
	{
		$cacheTTL = $cacheTTL ?? $this->ttl;

		/* are we using the window option? */
		if ($useWindow) {
			/*
			let's determine the window size based on there cache time to live length no more than 5 minutes
			if your traffic to the cache data is that light then cache stampede shouldn't be a problem
			*/
			$window = min(300, ceil($cacheTTL * .02));

			/* add it to the cache_ttl to get our "new" cache time to live */
			$cacheTTL += mt_rand(-$window, $window);
		}

		return $cacheTTL;
	}

	public function isSupported(): bool
	{
		return true;
	}
} /* end class */
