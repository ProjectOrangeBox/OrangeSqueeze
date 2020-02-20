<?php

namespace projectorangebox\quickResponse;

interface quickResponseInterface
{

	public function __construct(array $config);
	public function header(int $statusCode, string $type): quickResponseInterface;
	public function type(string $type): quickResponseInterface;
	public function code(int $statusCode): quickResponseInterface;
	public function view(string $view): quickResponseInterface;
	public function format(array $array, string $view = null): string;
	public function display(array $array, string $view = null): void;
}
