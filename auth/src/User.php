<?php

namespace projectorangebox\auth;

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
		$this->config = $config;

		$this->UserACLConstruct($config);
		$this->UserSessionConstruct($config);
	}

	public function set(int $userId, bool $save = true): bool
	{
		$this->id = $userId;

		$this->refresh();

		if ($save) {
			$this->save();
		}

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
