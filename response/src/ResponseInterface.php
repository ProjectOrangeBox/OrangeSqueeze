<?php

namespace projectorangebox\response;

interface ResponseInterface
{
	public function get(): string;
	public function set(string $output): ResponseInterface;
	public function append(string $output): ResponseInterface;
	public function display(string $output = null): void;
	public function setRespondsCode(int $code): ResponseInterface;
	public function header(string $string, bool $replace = true, int $http_response_code = null): ResponseInterface;
	public function exit(int $status = 0): void;
}
