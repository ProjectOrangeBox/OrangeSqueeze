<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_email extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		$field = filter_var($field, FILTER_SANITIZE_EMAIL);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
