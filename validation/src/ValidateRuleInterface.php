<?php

namespace projectorangebox\validation;

interface ValidateRuleInterface
{
	public function validate(&$field, string $options): bool;
}
