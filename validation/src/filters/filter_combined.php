<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_combined extends ValidateFilterAbstract implements ValidateFilterInterface
{
	/*
	combined[{fielda},{fieldb},::,{fieldc}]
	 */
	public function filter(&$field, $options)
	{
		$fields = explode(',', $options);

		foreach ($fields as $f) {
			if (substr($f, 0, 1) == '{' && substr($f, -1) == '}') {
				$combined .= $this->field_data[substr($f, 1, -1)];
			} else {
				$combined .= $f;
			}
		}

		$field = $combined;
	}
} /* end class */
