<?php

namespace projectorangebox\response;

class Response implements ResponseInterface
{
	/* storage for final output */
	protected $finalOutput = '';

	/* get the current response output buffer */
	public function get(): string
	{
		return $this->finalOutput;
	}

	/* replace final output */
	public function set(string $output): ResponseInterface
	{
		$this->finalOutput = $output;

		return $this;
	}

	/* append to final output */
	public function append(string $output): ResponseInterface
	{
		$this->finalOutput .= $output;

		return $this;
	}

	/* echo final output */
	public function display(string $output = null, int $status_code = 0): void
	{
		if ($output) {
			$this->finalOutput = $output;
		}

		echo $this->finalOutput;

		$this->exit($status_code);
	}

	/* Set the HTTP response code */
	public function respondsCode(int $code): ResponseInterface
	{
		http_response_code($code);

		return $this;
	}

	public function contentType(string $mime_type, string $charset = 'UTF-8'): ResponseInterface
	{
		$this->header('Content-Type: ' . $mime_type . '; charset=' . $charset);

		return $this;
	}

	/* Send a raw HTTP header */
	public function header(string $string, bool $replace = true, int $http_response_code = null): ResponseInterface
	{
		header($string, $replace, $http_response_code);

		return $this;
	}

	/* final exit */
	public function exit(int $status = 0): void
	{
		exit($status);
	}
} /* end class */
