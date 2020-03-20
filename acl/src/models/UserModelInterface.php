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

namespace projectorangebox\acl\models;

interface UserModelInterface
{
	public function insert(array $columns): int;
	public function update(array $columns): int;
	public function delete($id): int;
	public function relink(int $userId, array $roleIds): bool;
} /* end interface */
