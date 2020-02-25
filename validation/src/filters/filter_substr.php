<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_substr extends ValidateFilterAbstract implements ValidateFilterInterface
{
	/* copy[field] */
	public function filter(&$field, $options)
	{
		list($a, $b) = explode($options, 2);

		$field = substr($field, $a, $b);
	}
} /* end class */
