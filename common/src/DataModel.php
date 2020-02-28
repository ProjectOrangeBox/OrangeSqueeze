<?php

namespace projectorangebox\common;

use Exception;
use projectorangebox\validation\ValidateInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class DataModel
{
	protected $config;
	protected $validateService;

	protected $errorsArray = [];
	protected $errorCode = 0;

	public function __construct(array $config = [])
	{
		$this->config = $config;

		$this->validateService = $config['validateService'];

		if (!($this->validateService instanceof ValidateInterface)) {
			throw new IncorrectInterfaceException('ValidateInterface');
		}
	}

	protected function validate(array &$data, string $ruleSets = null): bool
	{
		$this->errorCode = 0;

		/* Validation Errors */
		$this->errorsArray = $this->validateService->rules($this->getRuleSet($ruleSets, $this->rules), $data)->errors();

		if (!$this->success()) {
			/* use standard http codes - 406 not acceptable */
			$this->errorCode = 406;
		}

		return $this->success();
	}

	public function onlyColumns(array $columns, array $match)
	{
		return \array_intersect_key($columns, $match);
	}

	public function errors(): array
	{
		return $this->errorsArray;
	}

	public function errorCode(): int
	{
		return $this->errorCode;
	}

	public function success(): bool
	{
		return (count($this->errorsArray) == 0);
	}

	protected function getRuleSet(string $set = null, array $rules): array
	{
		$sets = ($set) ? explode(',', $set) : array_keys($rules);

		$ary = [];

		foreach ($sets as $name) {
			if (!array_key_exists($name, $this->rules)) {
				throw new Exception('No Rule found for "' . $name . '".');
			}

			$ary[$name] = $this->rules[$name];
		}

		return $ary;
	}
}
