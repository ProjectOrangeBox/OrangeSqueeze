<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilter;
use projectorangebox\validation\ValidateFilterInterface;

class filter_lowercase extends ValidateFilter implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = strtolower($field);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
