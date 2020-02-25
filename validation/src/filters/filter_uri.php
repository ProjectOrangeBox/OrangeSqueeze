<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_uri extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$field = '/' . trim(trim(strtolower($field)), '/');
		$field = preg_replace("#^/^[0-9a-z_*/]*$#", '', $field);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
