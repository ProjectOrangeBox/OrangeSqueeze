<?php

namespace projectorangebox\auth;

use projectorangebox\auth\models\RoleModelInterface;
use projectorangebox\auth\models\PermissionModelInterface;
use projectorangebox\auth\models\UserModelInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Acl implements AclInterface
{
	protected $config;

	public $role;
	public $roles;

	public $permission;
	public $permissions;

	public $user;
	public $users;

	public function __construct(array $config)
	{
		/*
		$this->config = $config;

		$class = $this->config['role model class'] ?? '\projectorangebox\auth\models\RoleModel';

		$this->role = $this->roles = new $class($config['database'], $config);

		if (!($this->roles instanceof RoleModelInterface)) {
			throw new IncorrectInterfaceException('roleModelInterface');
		}

		$class = $this->config['permission model class'] ?? '\projectorangebox\auth\models\PermissionModel';

		$this->permission = $this->permissions = new $class($config['database'], $config);

		if (!($this->permissions instanceof PermissionModelInterface)) {
			throw new IncorrectInterfaceException('permissionModelInterface');
		}

		$class = $this->config['user model class'] ?? '\projectorangebox\auth\models\UserModel';

		$this->user = $this->users = new $class($config['database'], $config);

		if (!($this->users instanceof UserModelInterface)) {
			throw new IncorrectInterfaceException('UserModelInterface');
		}
		*/
	}
}
