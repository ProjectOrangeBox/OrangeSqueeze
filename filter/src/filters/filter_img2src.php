<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_img2src extends FilterRuleAbstract implements FilterRuleInterface
{

	public function filter(&$field, string $options = ''): void
	{
		$column = (!empty($options)) ? $options : 'src';

		if (preg_match('#src="([^"]+)"#', $field, $match)) {
			$field = $match[1];
		}
	}
} /* end class */
