<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_replace extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		/* built the key value pair */
		$items  = explode(',', $options);
		$idx    = 0;
		$keys   = [];
		$values = [];

		foreach ($items as $item) {
			$idx++;
			if ($idx % 2) {
				$keys[] = $item;
			} else {
				$values[] = $item;
			}
		}

		if (count($keys) > 0 && count($values) > 0) {
			$field = str_replace($keys, $values, $field);
		}
	}
} /* end class */
