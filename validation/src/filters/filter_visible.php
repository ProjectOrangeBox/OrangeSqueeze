<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_visible extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $field);

		/* options is max length - filter is in orange core */
		$this->field($field);
		$this->length($options);
	}
} /* end class */
