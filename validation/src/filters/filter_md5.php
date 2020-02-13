<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilter;
use projectorangebox\validation\ValidateFilterInterface;

class filter_md5 extends ValidateFilter implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = md5($field);
	}
} /* end class */
