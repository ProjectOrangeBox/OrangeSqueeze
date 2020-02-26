<?php

namespace projectorangebox\formatter;

interface formatterInterface
{

	public function __construct(array $config);
	public function send(int $statusCode, string $type, string $charset = 'UTF-8'): formatterInterface;
	public function format(array $array = [], string $view = null): string;
}
