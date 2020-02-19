<?php

namespace projectorangebox\error;

use projectorangebox\error\Formatter;

class Errors implements ErrorsInterface
{
	const ROOT = '@root';

	protected $duplicates = []; /* prevent duplicates */
	protected $errors = []; /* main array */

	protected $group = 'errors';

	public $display; /* pass thru */

	public function __construct(array $config)
	{
		$this->group = $this->config['group'] ?? $this->group; /* default group */

		$this->display = new Formatter($config, $this);
	}

	public function setGroup(string $group): ErrorsInterface
	{
		$this->group = $group;

		return $this;
	}

	public function getGroup(): string
	{
		return $this->group;
	}

	public function getGroups($groups = null): array
	{
		/* get array of group names */
		return $this->extract_groups((array) $groups, true);
	}

	/* get array of groups */
	public function get($groups = null): array
	{
		return $this->extract_groups((array) $groups, false);
	}

	public function add(string $index, $value, string $group = null): ErrorsInterface
	{
		$group = ($group) ?? $this->group;

		$dupKey = md5($group . $index . json_encode($value));

		if (!isset($this->duplicates[$dupKey])) {
			if ($group == Errors::ROOT) {
				$this->errors[$index] = $value;
			} else {
				$this->errors[$group][$index] = $value;
			}

			$this->duplicates[$dupKey] = true;
		}

		return $this;
	}

	public function has($groups = null): bool
	{
		foreach ($this->extract_groups((array) $groups, true) as $key) {
			if (is_array($this->errors[$key])) {
				if (count($this->errors[$key])) {
					return true;
				}
			}
		}

		return false;
	}

	public function clear($groups = null): ErrorsInterface
	{
		foreach ($this->extract_groups((array) $groups, true) as $key) {
			$this->errors[$key] = [];
		}

		return $this;
	}

	/* protected */

	protected function extract_groups(array $groups = null, bool $keys): array
	{
		/* if null return entire error collection */
		if ($groups === null) {
			$errors = $this->errors;
		} elseif (is_array($groups)) {
			foreach ($groups as $key) {
				$errors[$key] = $this->errors[$key] ?? [];
			}
		} else {
			$errors[$groups] = $this->errors[$groups] ?? [];
		}

		return ($keys) ? array_keys($errors) : $errors;
	}
} /* end class */
