<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_datauri extends ValidateFilterAbstract implements ValidateFilterInterface
{

	public function filter(&$field, $options)
	{
		$field = ci('data_uri')->extract_data_img($field);
	}
} /* end class */
