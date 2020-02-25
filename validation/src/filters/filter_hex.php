<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_hex extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$field = preg_replace('/[^0-9a-f]+/', '', strtolower(trim($field)));

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
