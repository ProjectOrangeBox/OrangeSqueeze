<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilterAbstract;
use projectorangebox\validation\ValidateFilterInterface;

class filter_slug extends ValidateFilterAbstract implements ValidateFilterInterface
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
