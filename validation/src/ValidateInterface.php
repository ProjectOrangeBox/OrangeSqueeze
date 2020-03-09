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

namespace projectorangebox\validation;

interface ValidateInterface
{
	public function success(): bool;
	public function errors(): array;
	public function clear(): ValidateInterface;
	public function attachRule(string $name, \closure $closure): ValidateInterface;
	public function rule(string $rules, &$field, string $human = null): ValidateInterface;
	public function rules(array $multipleRules = [], array &$fields): ValidateInterface;
}
