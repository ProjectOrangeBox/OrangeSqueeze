<?php

namespace projectorangebox\response;

interface ResponseInterface
{
	public function __construct(array &$config);
	public function get(): string;
	public function set(string $output): ResponseInterface;
	public function append(string $output): ResponseInterface;
	public function display(string $output = null, int $statusCode = 0): void;
	public function contentType(string $mime_type, string $charset = 'UTF-8'): ResponseInterface;
	public function respondsCode(int $code): ResponseInterface;
	public function header(string $string, bool $replace = true, int $http_response_code = null): ResponseInterface;
	public function exit(int $status = 0): void;
}
