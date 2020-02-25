<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_int extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$pos = strpos($field, '.');

		if ($pos !== false) {
			$field = substr($field, 0, $pos);
		}

		$field  = preg_replace('/[^\-\+0-9]+/', '', $field);
		$prefix = ($field[0] == '-' || $field[0] == '+') ? $field[0] : '';
		$field  = $prefix . preg_replace('/[^0-9]+/', '', $field);

		$this->field($field);
		$this->length($options);
	}
}
