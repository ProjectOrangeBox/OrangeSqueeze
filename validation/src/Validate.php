<?php

namespace projectorangebox\validation;

use Exception;
use projectorangebox\filter\FilterInterface;
use projectorangebox\validation\exceptions\RuleNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Validate implements ValidateInterface
{
	protected $attached = [];
	protected $error_string = '';
	protected $error_human = '';
	protected $error_params = '';
	protected $error_field_value = '';
	protected $field_data = [];
	protected $config = [];
	protected $rulesClasses = [];
	protected $errors = [];
	protected $filterService;
	protected $filterRegex = ''; // prefix match ie. ';^filter_(.*)$;';

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->config = $config;
		$this->rulesClasses = $config['rules'];

		if (isset($config['filterService'])) {
			$this->filterService = $config['filterService'];

			if (!($this->filterService instanceof FilterInterface)) {
				throw new IncorrectInterfaceException('FilterInterface');
			}

			$this->filterRegex = $this->config['filter match regular expression'] ?? $this->filterRegex;
		}
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
		$this->attached[strtolower($name)] = $closure;

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
		$rules = $this->breakApartRules($rules);

		/* do we have any rules? */
		if (count($rules)) {
			/* field value before any validations / filters */
			$this->error_field_value = $field;

			/* yes - for each rule...*/
			foreach ($rules as $rule) {
				\log_message('info', 'Validate Rule ' . $rule . ' "' . $field . '" ' . $human);

				/* no rule? exit processing of the $rules array */
				if (empty($rule)) {
					\log_message('info', 'Validate no validation rule.');

					$success = true;
					break;
				}

				/* do we have this special rule? */
				if ($rule == 'allow_empty') {
					\log_message('info', 'Validate allow_empy skipping the rest if empty.');

					if (empty($field)) {
						/* end processing of the $rules array */
						break;
					} else {
						/* skip the rest of the current foreach but don't stop processing the $rules array  */
						continue;
					}
				}

				/* Grab parameters and rewrite rule if nessesary? */
				$parameters = $this->getParameters($rule);

				/* try to make the errors a little more human? */
				$this->makePresentableErrors($human, $rule, $parameters);

				/* does this match a option filter regular expression? */
				if (preg_match($this->filterRegex, $rule)) {
					if ($this->filterService) {
						$field = $this->filterService->filter($rule, $field);

						$success = true;
					} else {
						throw new Exception('Filter Service not provided to Validate Service there for you can not use Filters as rules.');
					}
				} else {
					/* default to validation rule */
					$success = $this->_validation($field, $rule, $parameters);
				}

				\log_message('info', 'Validate Success ' . $success);

				/* bail on first failure */
				if ($success === false) {
					/* end processing of the $rules array */
					break;
				}
			}
		}

		return $this;
	}

	/* protected */

	protected function _validation(&$field, string $rule, string $parameters = null): bool
	{
		$class_name = strtolower($rule);

		/* default error */
		$this->error_string = '%s is not valid.';

		if (isset($this->attached[$class_name])) {
			$success = $this->attached[$class_name]($field, $parameters, $this->error_string, $this->field_data, $this);
		} elseif (\class_exists($rule)) {
			$classInstance = new $rule($this->field_data, $this->error_string);

			if (!($classInstance instanceof ValidateRuleInterface)) {
				throw new IncorrectInterfaceException('ValidateRuleInterface');
			}

			$success = $classInstance->validate($field, $parameters);
		} elseif (isset($this->rulesClasses[$class_name])) {
			$fullClassName = $this->rulesClasses[$class_name];
			$classInstance = new $fullClassName($this->field_data, $this->error_string);

			if (!($classInstance instanceof ValidateRuleInterface)) {
				throw new IncorrectInterfaceException('ValidateRuleInterface');
			}

			$success = $classInstance->validate($field, $parameters);
		} elseif (function_exists($class_name)) {
			$success = ($parameters) ? $class_name($field, $parameters) : $class_name($field);
		} else {
			throw new RuleNotFoundException($rule);
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

	protected function getParameters(&$rule): string
	{
		/* setup default of no parameters */
		$parameters = '';

		/* do we have parameters if so split them out */
		if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
			$rule  = $match[1];
			$parameters = $match[2];
		}

		return $parameters;
	}

	protected function makePresentableErrors($human, $rule, $parameters): void
	{
		/* do we have a human readable field name? if not then try to make one */
		$this->error_human = ($human) ? $human : strtolower(str_replace('_', ' ', $rule));

		\log_message('info', 'Validate ' . $rule . '[' . $parameters . '] > ' . $this->error_human);

		/* try to format the parameters into something human readable incase they need this in there error message  */
		if (strpos($parameters, ',') !== false) {
			$this->error_params = str_replace(',', ', ', $parameters);

			if (($pos = strrpos($this->error_params, ', ')) !== false) {
				$this->error_params = substr_replace($this->error_params, ' or ', $pos, 2);
			}
		} else {
			$this->error_params = $parameters;
		}
	}

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
} /* end class */
