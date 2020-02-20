<?php

namespace projectorangebox\error;

interface FormatterInterface
{

	public function __construct(array $config, ErrorsInterface $errorsService);
	public function status($statusCode): FormatterInterface;
	public function view(string $viewfile): FormatterInterface;
	public function contentType(string $mimeType, string $charset = 'utf-8'): FormatterInterface;
	public function viewPrefix(string $viewPrefix): FormatterInterface;
	public function addView(string $view, string $path): FormatterInterface;
	public function onError($groups = null): void;
	public function error($groups = null): void;
}
