<?php

namespace projectorangebox\filter;

use projectorangebox\filter\FilterRuleInterface;

abstract class FilterRuleAbstract
{
	protected $true_array = [1, '1', 'y', 'on', 'yes', 't', 'true', true];
	protected $false_array = [0, '0', 'n', 'off', 'no', 'f', 'false', false];

	/* extend this function */
	public function filter(&$field, string $options = ''): void
	{
	}

	public function field(&$field): FilterRuleAbstract
	{
		$this->field = &$field;

		return $this;
	}

	public function length($length = null): FilterRuleAbstract
	{
		if (is_numeric($length)) {
			if ((int) $length > 0) {
				$this->field = substr($this->field, 0, $length);
			}
		}

		return $this;
	}

	public function trim(): FilterRuleAbstract
	{
		$this->field = trim($this->field);

		return $this;
	}

	public function human(): FilterRuleAbstract
	{
		$this->field = preg_replace("/[^\\x20-\\x7E]/mi", '', $this->field);

		return $this;
	}

	public function human_plus(): FilterRuleAbstract
	{
		$this->field = preg_replace("/[^\\x20-\\x7E\\n\\t\\r]/mi", '', $this->field);

		return $this;
	}

	public function strip($strip): FilterRuleAbstract
	{
		$this->field = str_replace(str_split($strip), '', $this->field);

		return $this;
	}

	public function is_bol($field): bool
	{
		return (in_array(strtolower($field), array_merge($this->true_array, $this->false_array), true)) ? true : false;
	}
}
