<?php

namespace projectorangebox\auth;

use Exception;
use projectorangebox\fake\Cache;
use projectorangebox\cache\CacheInterface;

class User implements UserInterface
{
	use UserACLTrait;
	use UserSessionTrait;

	protected $id; /* primary key */

	protected $email;
	protected $username;
	protected $isActive;
	protected $dashboardUrl;
	protected $meta;

	protected $readRoleId;
	protected $editRoleId;
	protected $deleteRoleId;

	public function __construct(array $config)
	{
		/* defaults */
		$defaults = [
			'empty fields error' => 'Missing Required Field',
			'general failure error' => 'Login Error',
			'User Auth Class' => '\projectorangebox\auth\Auth',
			'User Model Class' => '\projectorangebox\auth\UserAuthModel',
		];

		$this->config = array_replace($defaults, $config);

		/* check for required */
		$required = ['admin user', 'guest user', 'admin role', 'everyone role'];

		foreach ($required as $key) {
			if (!isset($this->config[$key])) {
				throw new Exception('The required configuration value "' . $key . '" is not set.');
			}
		}

		if (!isset($this->config['cacheService'])) {
			$this->config['cacheService'] = new Cache([]);
		}

		if (!($this->config['cacheService'] instanceof CacheInterface)) {
			throw new Exception('Cache Service is not an instance of CacheInterface.');
		}

		$this->UserACLTraitConstruct($config);
		$this->UserSessionTraitConstruct($config);
	}

	public function setUserId(int $userId): bool
	{
		$this->id = $userId;

		$this->refresh();

		$this->save();

		return true;
	}

	public function __debugInfo()
	{
		$this->lazyLoad();

		return [
			'id' => $this->id,
			'email' => $this->email,
			'username' => $this->username,
			'is active' => $this->isActive,
			'dashboard Url' => $this->dashboardUrl,
			'meta' => $this->meta,
			'read/write' => [
				'read' => $this->readRoleId,
				'edit' => $this->editRoleId,
				'delete' => $this->deleteRoleId,
			],
			'roles' => $this->roles(),
			'permissions' => $this->permissions(),
		];
	}
} /* end class */
