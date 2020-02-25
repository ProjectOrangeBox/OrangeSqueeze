<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_phone extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		/* this needs to be passed by reference */
		$field = preg_replace('/[^0-9x]+/', ' ', $field);
		$field = preg_replace('/ {2,}/', ' ', $field);

		/* $field pass by ref,options is the length */
		$this->field($field)->human()->length($options);
	}
} /* end class */
