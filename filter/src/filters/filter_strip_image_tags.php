<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_strip_image_tags extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		return preg_replace(['#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i', '#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'], '\\2', $field);
	}
} /* end class */
