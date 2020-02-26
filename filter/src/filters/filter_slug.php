<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_slug extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = preg_replace('~[^\pL\d]+~u', '-', $field);
		$field = iconv('utf-8', 'us-ascii//TRANSLIT', $field);
		$field = preg_replace('~[^-\w]+~', '', $field);
		$field = trim($field, '-');
		$field = preg_replace('~-+~', '-', $field);
		$field = strtolower($field);
	}
}
