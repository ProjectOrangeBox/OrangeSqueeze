<?php

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
