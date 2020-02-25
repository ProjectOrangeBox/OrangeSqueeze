<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_array_explode extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$options = ($options) ? $options : ' ';

		if (is_string($field)) {
			$field = explode($options, $field);
		}
	}
} /* end class */
