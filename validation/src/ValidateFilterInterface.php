<?php

namespace projectorangebox\validation;

interface ValidateFilterInterface
{
	public function filter(&$field, string $options = ''): void;
}
