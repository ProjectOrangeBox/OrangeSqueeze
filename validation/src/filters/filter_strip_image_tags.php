<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_strip_image_tags extends ValidateFilterAbstract implements ValidateFilterInterface
{
	public function filter(&$field, $options)
	{
		return preg_replace(['#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i', '#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'], '\\2', $field);
	}
} /* end class */
