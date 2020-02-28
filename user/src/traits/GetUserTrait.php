<?php

namespace projectorangebox\user\traits;

use PDO;

trait GetUserTrait
{
	protected function lazyLoad(): void
	{
		if (!$this->lazyLoaded) {
			$this->getUser($this->id);

			$this->lazyLoaded = true;
		}
	}

	protected function getUser(int $userId): void
	{
		$userRecord = $this->_getUserCached($userId);

		if ($userRecord) {
			$this->username = $userRecord['username'];
			$this->email = $userRecord['email'];

			/* don't need to keep this around */
			unset($userRecord['password']);

			$this->meta = $userRecord;

			$rolesPermissions = $this->_getRolesPermissionsCached($userId);

			$this->roles = (array) $rolesPermissions['roles'];
			$this->permissions = (array) $rolesPermissions['permissions'];
		}
	}

	protected function _getUserCached(int $userId)
	{
		$cacheKey = 'user.id.' . $userId . '.details';

		if (!$record = $this->cacheService->get($cacheKey)) {
			$record = $this->query('select * from ' . $this->userTable . ' where id = :userid limit 1', [':userid' => (int) $userId]);

			if ($record) {
				$this->cacheService->save($cacheKey, $record);
			}
		}

		return $record;
	}

	protected function _getRolesPermissionsCached(int $userId): array
	{
		$cacheKey = 'user.id.' . $userId . '.roles.permissions';

		if (!$record = $this->cacheService->get($cacheKey)) {
			$record = $this->_getRolesPermissions($userId);

			if ($record) {
				$this->cacheService->save($cacheKey, $record);
			}
		}

		return $record;
	}

	protected function _getRolesPermissions(int $userId): array
	{
		$rolesPermissions = [];

		$sql = "select
			`user_id`,
			`" . $this->roleTable . "`.`id` `orange_roles_id`,
			`" . $this->roleTable . "`.`name` `orange_roles_name`,
			`" . $this->rolePermissionTable . "`.`permission_id` `orange_permission_id`,
			`" . $this->permissionTable . "`.`key` `orange_permission_key`
			from " . $this->userRoleTable . "
			left join " . $this->roleTable . " on " . $this->roleTable . ".id = " . $this->userRoleTable . ".role_id
			left join " . $this->rolePermissionTable . " on " . $this->rolePermissionTable . ".role_id = " . $this->roleTable . ".id
			left join " . $this->permissionTable . " on " . $this->permissionTable . ".id = " . $this->rolePermissionTable . ".permission_id
			where " . $this->userRoleTable . ".user_id = :userid";

		$dbc = $this->query($sql, [':userid' => (int) $userId]);

		if ($dbc) {
			while ($dbr = $dbc->fetchObject()) {
				if ($dbr->orange_roles_name) {
					if (!empty($dbr->orange_roles_name)) {
						$rolesPermissions['roles'][(int) $dbr->orange_roles_id] = $dbr->orange_roles_name;
					}
				}
				if ($dbr->orange_permission_key) {
					if (!empty($dbr->orange_permission_key)) {
						$rolesPermissions['permissions'][(int) $dbr->orange_permission_id] = $dbr->orange_permission_key;
					}
				}
			}
		}

		/* everybody */
		$rolesPermissions['roles'][$this->everyoneRoleId] = 'Everyone';

		return $rolesPermissions;
	}

	/* PDO simple as beans select query wrapper */
	protected function query(string $sql, array $execute = [], $onEmpty = false)
	{
		$query = $this->db->prepare($sql);
		$query->execute($execute);
		$records = $query->fetchAll(PDO::FETCH_ASSOC);

		switch (count($records)) {
			case 0:
				$return = $onEmpty;
				break;
			case 1:
				$return = $records[0];
				break;
			default:
				$return = $records;
		}

		return $return;
	}
}
