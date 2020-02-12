<?php

namespace projectorangebox\error;

use projectorangebox\view\ViewInterface;
use projectorangebox\response\ResponseInterface;

interface ErrorsInterface
{

	public function __construct(array $config, ViewInterface $viewService, ResponseInterface $responseService);
	public function setGroup(string $group): ErrorsInterface;
	public function getGroup(): string;
	public function getGroups($groups = null): array;

	public function add(string $index, $value, string $group = null): ErrorsInterface;
	public function has($groups = null): bool;
	public function clear($groups = null): ErrorsInterface;

	public function viewPrefix(string $viewPrefix): ErrorsInterface;
	public function view(string $view): ErrorsInterface;
	public function addView(string $view, string $value): ErrorsInterface;

	public function get($groups = null); /* mixed */

	public function contentType(string $mimeType, string $charset = 'utf-8'): ErrorsInterface;

	public function displayOnError($groups = null, int $statusCode = 500, string $view = null): void;
	public function displayError($groups = null, int $statusCode = 500, string $view = null): void;
	public function display(string $title, string $body, int $statusCode = 500, string $view = null): void;
}
