<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_length extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$this->field($field);
		$this->length($options);
	}
}
