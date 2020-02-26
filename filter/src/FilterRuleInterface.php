<?php

namespace projectorangebox\filter;

interface FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void;
}
