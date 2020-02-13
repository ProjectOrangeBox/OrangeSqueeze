<?php

namespace projectorangebox\validation;

use projectorangebox\validation\ValidateRule;

abstract class ValidateFilter extends ValidateRule implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
	}
}
