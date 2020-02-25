<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_url extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		if ($field === 'http://' or $field === '') {
			$field = '';
		}

		if (strpos($field, 'http://') !== 0 && strpos($field, 'https://') !== 0) {
			$field = 'http://' . $field;
		}
	}
} /* end class */
