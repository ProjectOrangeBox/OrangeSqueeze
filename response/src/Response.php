<?php

namespace projectorangebox\response;

class Response implements ResponseInterface
{
	protected $finalOutput = '';

	public function get(): string
	{
		return $this->finalOutput;
	}

	public function set(string $output): ResponseInterface
	{
		$this->finalOutput = $output;

		return $this;
	}

	public function append(string $output): ResponseInterface
	{
		$this->finalOutput .= $output;

		return $this;
	}

	public function display(string $output = null): void
	{
		if ($output) {
			$this->finalOutput = $output;
		}

		echo $this->finalOutput;

		$this->exit(0);
	}

	public function setRespondsCode(int $code): ResponseInterface
	{
		http_response_code($code);

		return $this;
	}

	public function header(string $string, bool $replace = true, int $http_response_code = null): ResponseInterface
	{
		header($string, $replace, $http_response_code);

		return $this;
	}

	public function exit(int $status = 0): void
	{
		exit($status);
	}
} /* end class */
