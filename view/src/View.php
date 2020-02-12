<?php

namespace projectorangebox\view;

use Exception;
use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;

class View implements ViewInterface
{
	protected $config;
	protected $parser;
	protected $fourohfour = '';
	protected $reparseKey;
	protected $reparseData = [];

	public function __construct(array $config)
	{
		$this->config = $config;

		$this->fourohfour = $config['404'];

		if (!isset($this->config['parsers'])) {
			throw new Exception('No parsers passed into view.');
		}
	}

	/* get handler for extension */
	public function __get(string $extension): ParserInterface
	{
		$extension = $this->normalizeExtension($extension);

		if (!\array_key_exists($extension, $this->config['parsers'])) {
			throw new Exception('Parser for ' . $extension . ' not found.');
		}

		return $this->config['parsers'][$extension];
	}

	/* set handler for extension */
	public function __set(string $extension, ParserInterface $parser)
	{
		$this->config['parsers'][$this->normalizeExtension($extension)] = $parser;
	}

	public function reparse(string $key, array $data = []): ViewInterface
	{
		$this->reparseKey = $key;

		$this->reparseData = $data;

		return $this;
	}

	public function parse(string $key, array $data = []): string
	{
		/* parse the router provided template */
		$html = $this->_parse($key, $data);

		/**
		 * If somewhere on the orginal template they set the reparseKey
		 * we need to re-parse the new template with the same data
		 * this replaces the current output
		 */
		while ($this->reparseKey) {
			$data = array_replace($data, $this->reparseData);

			/* clear it so we don't loop */
			unset($this->reparseKey);

			$html = $this->_parse($this->reparseKey, $data);
		}

		/* return the output */
		return $html;
	}

	public function parse_string(string $string, string $extension, array $data = []): string
	{
		$extension = $this->normalizeExtension($extension);

		if (!\array_key_exists($extension, $this->config['parsers'])) {
			throw new Exception('Parse String parser for ' . $extension . ' not found.');
		}

		return $this->config['parsers'][$extension]->parse_string($string, $data);
	}

	protected function _parse(string $key, array $data = []): string
	{
		$key = $this->normailizedKey($key);
		$extension = $this->find($key);

		if (empty($extension)) {
			$key = $this->normailizedKey($this->fourohfour);
			$extension = $this->find($key);

			if (empty($extension)) {
				throw new Exception($key . ' or ' . $this->fourohfour);
			}
		}

		return $this->config['parsers'][$extension]->parse($key, $data);
	}

	protected function normailizedKey(string $key): string
	{
		return strtolower(trim($key, '/'));
	}

	protected function normalizeExtension(string $extension): string
	{
		return strtolower(trim($extension, '.'));
	}

	protected function find(string $key): string
	{
		$key = $this->normailizedKey($key);

		$match = '';

		foreach ($this->config['parsers'] as $extension => $parser) {
			if ($parser->exists($key)) {
				$match = $extension;
				break;
			}
		}

		return $match;
	}
}
