<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\response;

class Response implements ResponseInterface
{
	/* storage for final output */
	protected $finalOutput = '';
	protected $headerSend = false;
	protected $http_response_code = [];
	protected $header = [];
	protected $config = [];

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->config = $config;
	}

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
	public function display(string $output = null, int $statusCode = 0): void
	{
		if (!$this->headerSend) {
			$this->sendHeader();
		}

		if ($output) {
			$this->finalOutput .= $output;
		}

		/* final echo */
		echo $this->finalOutput;

		$this->exit($statusCode);
	}

	/* Set the HTTP response code */
	public function respondsCode(int $code): ResponseInterface
	{
		$this->http_response_code[$code] = $code;

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
		$key = md5(\json_encode(\func_get_args()));

		$this->header[$key] = [$string, $replace, $http_response_code];

		return $this;
	}

	/* final exit */
	public function exit(int $status = 0): void
	{
		exit($status);
	}

	protected function sendHeader()
	{
		foreach ($this->http_response_code as $code) {
			http_response_code($code);
		}

		foreach ($this->header as $args) {
			header($args[0], $args[1], $args[2]);
		}

		$this->headerSend = true;
	}
} /* end class */
