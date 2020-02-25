<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_array_implode extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$options = ($options) ? $options : ' ';

		if (is_array($field)) {
			$field = implode($options, $field);
		}
	}
} /* end class */
