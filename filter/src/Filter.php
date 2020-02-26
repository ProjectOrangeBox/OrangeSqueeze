<?php

namespace projectorangebox\filter;

use projectorangebox\filter\exceptions\FilterNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Filter implements FilterInterface
{
	protected $attached = [];
	protected $config = [];
	protected $rulesClasses = [];

	public function __construct(array $config)
	{
		$this->config = $config;
		$this->rulesClasses = $config['rules'];
	}

	public function attachFilter(string $name, \closure $closure): FilterInterface
	{
		$this->attached[strtolower($name)] = $closure;

		return $this;
	}

	public function filter(string $rules, $field) /* mixed */
	{
		$rules = $this->breakApartRules($rules);

		foreach ($rules as $rule) {
			/* setup default of no parameters */
			$param = '';

			/* do we have parameters if so split them out */
			if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
				$rule  = $match[1];
				$param = $match[2];
			}

			$this->_filter($field, $rule, $param);
		}

		return $field;
	}

	/* passed and mofidied by reference */
	protected function _filter(&$field, string $rule, string $param = null): void
	{
		$className = strtolower($rule);

		if (isset($this->attached[$className])) {
			$this->attached[$className]($field, $param);
		} elseif (\class_exists($rule, true)) {
			$classInstance = new $rule($this->field_data);

			if (!($classInstance instanceof FilterRuleInterface)) {
				throw new IncorrectInterfaceException('FilterRuleInterface');
			}

			$classInstance->filter($field, $param);
		} elseif (isset($this->rulesClasses[$className])) {
			$fullClassName = $this->rulesClasses[$className];
			$classInstance = new $fullClassName($this->field_data);

			if (!($classInstance instanceof FilterRuleInterface)) {
				throw new IncorrectInterfaceException('FilterRuleInterface');
			}

			$classInstance->filter($field, $param);
		} elseif (function_exists($className)) {
			$field = ($param) ? $className($field, $param) : $className($field);
		} else {
			throw new FilterNotFoundException($rule);
		}
	}

	protected function breakApartRules($rules): array
	{
		/* break apart the rules */
		if (!is_array($rules)) {
			/* is this a preset set in the configuration array? */
			$rules = (isset($this->config[$rules])) ? $this->config[$rules] : $rules;

			/* split these into individual rules */
			if (is_string($rules)) {
				$rules = explode('|', $rules);
			}
		}

		return $rules;
	}
}
