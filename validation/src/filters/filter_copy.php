<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_copy extends ValidateFilterAbstract implements ValidateFilterInterface
{
	/* copy[field] */
	public function filter(&$field, $options)
	{
		$field = $this->field_data[$options];
	}
} /* end class */
