<?php

namespace projectorangebox\user\traits;

trait SessionTrait
{
	public function retrieve(): bool
	{
		$savedUserId = $this->sessionService->get($this->sessionKey, null);

		$userId = ((int) $savedUserId > 0) ? (int) $savedUserId : $this->guestUserId;

		$this->setUserId($userId);

		return true;
	}

	public function save(): bool
	{
		$this->sessionService->set($this->sessionKey, $this->id);

		return true;
	}

	public function flush(): bool
	{
		$this->lazyLoaded = false;

		return true;
	}
}
