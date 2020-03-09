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
