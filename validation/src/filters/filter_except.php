<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_except extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		/* options is what is stripped "except" */
		$field = preg_replace("/[^" . preg_quote($options, "/") . "]/", '', $field);
	}
} /* end class */
