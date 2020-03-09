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

namespace projectorangebox\models;

interface DatabaseModelInterface
{

	public function __construct(array &$config);
	public function get($primaryId);
	public function getBy(array $where);
	public function getMany();
	public function getManyBy(array $where);
	public function insert(array $columns): int;
	public function update(array $columns): int;
	public function updateBy(array $columns, array $where): int;
	public function delete($primaryId): int;
	public function deleteBy(array $where): int;
	public function columns($columns): DatabaseModelInterface;
	public function exists(array $where): bool;
	public function upsert(array $columns): int;
	public function upsertBy(array $columns, array $where): int;
	public function isUnique($columnName, $columnValue, $primary): bool;
	public function lastQuery(): string;
	public function orderBy($column, $value = 'ASC'): DatabaseModelInterface;
	public function limit(int $limit): DatabaseModelInterface;

	public function hasError(): bool;
	public function errorCode(): int;
	public function errors(): array;
}
