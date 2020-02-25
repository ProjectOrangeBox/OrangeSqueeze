<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_uppercase extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$field = strtoupper($field);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
