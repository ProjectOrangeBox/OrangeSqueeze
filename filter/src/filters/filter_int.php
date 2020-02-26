<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_int extends FilterRuleAbstract implements FilterRuleInterface
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
