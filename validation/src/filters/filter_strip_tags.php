<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_strip_tags extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$allowable_tags = (!empty($options)) ? $options : '';

		$field = strip_tags($field, $allowable_tags);
	}
} /* end class */
