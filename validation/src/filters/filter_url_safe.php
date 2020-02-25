<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_url_safe extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		/* $field pass by ref,options is the length */
		$this->field($field)->human()->length($options)->strip('~`!@$^()* {}[]|\;"\'<>,');
	}
} /* end class */
