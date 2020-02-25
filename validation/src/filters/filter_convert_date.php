<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_convert_date extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$options = ($options) ? $options : 'Y-m-d H:i:s';

		$field = date($options, strtotime($field));
	}
}
