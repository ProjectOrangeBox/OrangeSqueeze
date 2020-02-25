<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_img2src extends ValidateFilterAbstract implements ValidateFilterInterface
{

	public function filter(&$field, $options)
	{
		$column = (!empty($options)) ? $options : 'src';

		if (preg_match('#src="([^"]+)"#', $field, $match)) {
			$field = $match[1];
		}
	}
} /* end class */
