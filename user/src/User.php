<?php

namespace projectorangebox\user;

use PDO;
use Exception;
use projectorangebox\mock\Cache;
use projectorangebox\cache\CacheInterface;
use projectorangebox\user\traits\AccessTrait;
use projectorangebox\session\SessionInterface;
use projectorangebox\user\traits\GetUserTrait;
use projectorangebox\user\traits\SessionTrait;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class User implements UserInterface
{
	use AccessTrait;
	use SessionTrait;
	use GetUserTrait;

	protected $id; /* primary key integer */

	protected $email;
	protected $username;
	protected $isActive;
	protected $meta;

	protected $db;
	protected $sessionService;
	protected $cacheService;

	protected $sessionKey = 'user::id';

	protected $adminUserId = 0;
	protected $guestUserId = 0;
	protected $adminRoleId = 0;
	protected $everyoneRoleId = 0;

	protected $roles = [];
	protected $permissions = [];

	protected $lazyLoaded = false;

	protected $userTable;
	protected $roleTable;
	protected $userRoleTable;
	protected $rolePermissionTable;
	protected $permissionTable;

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		/* check for required */
		$required = [
			'admin user',
			'guest user',
			'admin role',
			'everyone role',
			'user table',
			'role table',
			'permission table',
			'user role table',
			'role permission table',
		];

		foreach ($required as $key) {
			if (!isset($config[$key])) {
				throw new Exception('The required configuration value "' . $key . '" is not set.');
			}
		}

		$this->userTable = $config['user table'];
		$this->roleTable = $config['role table'];
		$this->permissionTable = $config['permission table'];
		$this->userRoleTable = $config['user role table'];
		$this->rolePermissionTable = $config['role permission table'];

		$this->adminUserId = (int) $config['admin user'];
		$this->guestUserId = (int) $config['guest user'];
		$this->adminRoleId = (int) $config['admin role'];
		$this->everyoneRoleId = (int) $config['everyone role'];

		if (!isset($config['cacheService'])) {
			/* create fake cache handler */
			$this->cacheService = new Cache([]);
		} else {
			$this->cacheService = $config['cacheService'];
		}

		if (!($this->cacheService instanceof CacheInterface)) {
			throw new IncorrectInterfaceException('CacheInterface');
		}

		$this->sessionService = $config['sessionService'];

		if (!($this->sessionService instanceof SessionInterface)) {
			throw new IncorrectInterfaceException('SessionInterface');
		}

		$this->db = $config['db'];

		if (!($this->db instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}

		/* default as guest */
		$this->id = $this->guestUserId;

		/* unloaded */
		$this->lazyLoaded = false;

		/* try to restore session */
		$this->retrieve();
	}

	public function __get(string $name) /* mixed */
	{
		if (\in_array($name, ['id', 'username', 'email', 'meta', 'roles', 'permissions'])) {
			$this->lazyLoad();

			return $this->$name;
		}

		return null;
	}

	public function setUserId(int $userId): bool
	{
		$this->id = $userId;

		$this->flush();

		$this->save();

		return true;
	}

	public function setUserGuest(): bool
	{
		$this->id = $this->guestUserId;

		$this->flush();

		$this->save();

		return true;
	}

	public function debug()
	{
		$this->lazyLoad();

		return [
			'id' => $this->id,
			'email' => $this->email,
			'username' => $this->username,
			'is active' => $this->is_active,
			'meta' => $this->meta,
			'roles' => $this->roles,
			'permissions' => $this->permissions,
		];
	}
} /* end class */
