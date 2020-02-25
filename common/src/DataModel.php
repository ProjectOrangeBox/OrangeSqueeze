<?php

namespace projectorangebox\common;

use Exception;
use projectorangebox\validation\ValidateInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class DataModel
{
	protected $config;
	protected $validatorService;

	protected $errors = [];
	protected $errorCode = 0;

	public function __construct(array $config = [])
	{
		$this->config = $config;

		$this->config['validate service name'] = $this->config['validate service name'] ?? 'validate';
	}

	protected function validate(array &$data, string $ruleSets = null): bool
	{
		$this->errorCode = 0;

		$this->validatorService = service($this->config['validate service name']);

		if (!($this->validatorService instanceof ValidateInterface)) {
			throw new IncorrectInterfaceException($this->config['validate service name']);
		}

		/* Validation Errors */
		$this->errors = $this->validatorService->rules($this->getRules($ruleSets, $this->rules), $data)->errors();

		if (!$this->success()) {
			/* use standard http codes - 406 not acceptable */
			$this->errorCode = 406;
		}

		return $this->success();
	}

	public function errors(): array
	{
		return $this->errors;
	}

	public function errorCode(): int
	{
		return $this->errorCode;
	}

	public function success(): bool
	{
		return (count($this->errors) == 0);
	}

	protected function getRules(string $tests = null, array $rules): array
	{
		$tests = ($tests) ? explode(',', $tests) : array_keys($rules);

		$ary = [];

		foreach ($tests as $test) {
			if (!array_key_exists($test, $this->rules)) {
				throw new Exception('No Rule found for "' . $test . '".');
			}

			$ary[$test] = $this->rules[$test];
		}

		return $ary;
	}
}
