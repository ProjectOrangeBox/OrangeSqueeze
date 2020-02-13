<?php

namespace projectorangebox\validation\filters;

use projectorangebox\validation\ValidateFilter;
use projectorangebox\validation\ValidateFilterInterface;

class filter_json extends ValidateFilter implements ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void
	{
		if (is_array($field) || is_object($field)) {
			$field = json_encode($field, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		}
	}
} /* end class */
