<?php

namespace projectorangebox\validation;

use projectorangebox\validation\ValidateRuleAbstract;

abstract class ValidateFilterAbstract extends ValidateRuleAbstract
{
	public function filter(&$field, string $options = ''): void
	{
	}
}
