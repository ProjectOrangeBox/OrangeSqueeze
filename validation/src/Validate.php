<?php

namespace projectorangebox\validation;

use Exception;
use projectorangebox\validation\ValidateInterface;

class Validate implements ValidateInterface
{
	protected $attached = [];
	protected $error_string = '';
	protected $error_human = '';
	protected $error_params = '';
	protected $error_field_value = '';
	protected $field_data = [];
	protected $config = [];
	protected $rules = [];
	protected $errors = [];

	public function __construct(array $config)
	{
		$this->config = $config;
		$this->rules = $config['rules'];
	}

	public function success(): Bool
	{
		return !((bool) count($this->errors));
	}

	public function errors(): array
	{
		return $this->errors;
	}

	public function clear(): ValidateInterface
	{
		$this->errors = [];

		return $this;
	}

	public function attachRule(string $name, \closure $closure): ValidateInterface
	{
		$this->attached[$this->normalizeRule($name)] = $closure;

		return $this;
	}

	public function rules(array $multipleRules = [], array &$fields): ValidateInterface
	{
		/* save this as a reference for the validations and filters to use */
		$this->field_data = &$fields;

		/* process each field and rule as a single rule, field, and human label */
		foreach ($multipleRules as $fieldname => $record) {
			$rules = $record['rules'] ?? '';
			$field = &$fields[$fieldname] ?? '';
			$label = $record['label'] ?? null;

			$this->rule($rules, $field, $label);
		}

		/* break the reference */
		unset($this->field_data);

		/* now set it to empty */
		$this->field_data = [];

		return $this;
	}

	public function rule(string $rules, &$field, string $human = null): ValidateInterface
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

		/* do we have any rules? */
		if (count($rules)) {
			/* field value before any validations / filters */
			$this->error_field_value = $field;

			/* yes - for each rule...*/
			foreach ($rules as $rule) {
				\log_message('debug', 'Validate Rule ' . $rule . ' "' . $field . '" ' . $human);

				/* no rule? exit processing of the $rules array */
				if (empty($rule)) {
					\log_message('debug', 'Validate no validation rule.');

					$success = true;
					break;
				}

				/* do we have this special rule? */
				if ($rule == 'allow_empty') {
					\log_message('debug', 'Validate allow_empy skipping the rest if empty.');

					if (empty($field)) {
						/* end processing of the $rules array */
						break;
					} else {
						/* skip the rest of the current foreach but don't stop processing the $rules array  */
						continue;
					}
				}

				/* setup default of no parameters */
				$param = '';

				/* do we have parameters if so split them out */
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
					$rule  = $match[1];
					$param = $match[2];
				}

				/* do we have a human readable field name? if not then try to make one */
				$this->error_human = ($human) ? $human : strtolower(str_replace('_', ' ', $rule));

				\log_message('debug', 'Validate ' . $rule . '[' . $param . '] > ' . $this->error_human);

				/* try to format the parameters into something human readable incase they need this in there error message  */
				if (strpos($param, ',') !== false) {
					$this->error_params = str_replace(',', ', ', $param);

					if (($pos = strrpos($this->error_params, ', ')) !== false) {
						$this->error_params = substr_replace($this->error_params, ' or ', $pos, 2);
					}
				} else {
					$this->error_params = $param;
				}

				/* hopefully error_params looks presentable now? */

				/* take action on a validation or filter - filters MUST always start with "filter_" */
				$success = (substr(strtolower($rule), 0, 7) == 'filter_') ? $this->_filter($field, $rule, $param) : $this->_validation($field, $rule, $param);

				\log_message('debug', 'Validate Success ' . $success);

				/* bail on first failure */
				if ($success === false) {
					/* end processing of the $rules array */
					return $this;
				}
			}
		}

		return $this;
	}

	/* protected */

	protected function addError(string $fieldname): ValidateInterface
	{
		/**
		 * sprintf argument 1 human name for field
		 * sprintf argument 2 human version of options (computer generated)
		 * sprintf argument 3 field value
		 */
		$this->errors[$fieldname] = sprintf($this->error_string, $this->error_human, $this->error_params, $this->error_field_value);

		return $this;
	}

	protected function _filter(&$field, string $rule, string $param = null): bool
	{
		$class_name = $this->normalizeRule($rule);

		if (isset($this->attached[$class_name])) {
			$this->attached[$class_name]($field, $param);
		} elseif (isset($this->rules[$class_name])) {
			$full_class_name = $this->rules[$class_name];
			(new $full_class_name($this->field_data))->filter($field, $param);
		} elseif (function_exists($class_name)) {
			$field = ($param) ? $class_name($field, $param) : $class_name($field);
		} else {
			throw new Exception('Could not filter ' . $rule);
		}

		/* filters don't fail */
		return true;
	}

	protected function _validation(&$field, string $rule, string $param = null): bool
	{
		$class_name = $this->normalizeRule($rule);

		/* default error */
		$this->error_string = '%s is not valid.';

		if (isset($this->attached[$class_name])) {
			$success = $this->attached[$class_name]($field, $param, $this->error_string, $this->field_data, $this);
		} elseif (isset($this->rules[$class_name])) {
			$full_class_name = $this->rules[$class_name];
			$success = (new $full_class_name($this->field_data, $this->error_string))->validate($field, $param);
		} elseif (function_exists($class_name)) {
			$success = ($param) ? $class_name($field, $param) : $class_name($field);
		} else {
			throw new Exception('Could not validate ' . $rule);
		}

		/* if success is really really false then it's a error */
		if ($success === false) {
			$this->addError($this->error_human);
		} else {
			/* not a boolean then it's something useable */
			if (!is_bool($success)) {
				$field = $success;

				$success = true;
			}
		}

		return $success;
	}

	protected function normalizeRule(string $name): string
	{
		return strtolower($name);
	}
} /* end class */
