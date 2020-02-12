<?php

namespace projectorangebox\error;

use projectorangebox\response\ResponseInterface;

class ErrorsEchoResponse implements ResponseInterface
{
	/* storage for final output */
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

	public function display(string $output = null, int $status_code = 0): void
	{
		if ($output) {
			$this->finalOutput = $output;
		}

		echo $this->finalOutput;

		$this->exit($status_code);
	}

	public function contentType(string $mime_type, string $charset = 'UTF-8'): ResponseInterface
	{
		$this->header('Content-Type: ' . $mime_type . '; charset=' . $charset);

		return $this;
	}

	public function respondsCode(int $code): ResponseInterface
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
