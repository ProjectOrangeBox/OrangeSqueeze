<?php

namespace projectorangebox\view\parsers;

use FS;
use projectorangebox\view\parsers\ParserInterface;

class Ci implements ParserInterface
{

	protected $config = [];
	protected $views = [];
	protected $cacheFolder = '';
	protected $forceCompile = true;
	protected $delimiters = ['{', '}'];

	public function __construct(array &$config)
	{
		$this->views = $config['views'] ?? [];
		$this->cacheFolder = $config['cache folder'] ?? '/var/cache/codeigniter';
		$this->forceCompile = $config['forceCompile'] ?? DEBUG;

		FS::mkdir($this->cacheFolder);
	}

	/* string|array */
	public function setDelimiters($l = '{{', string $r = '}}'): ParserInterface
	{
		/* set delimiters */
		$this->delimiters = (is_array($l)) ? $l : [$l, $r];

		/* chain-able */
		return $this;
	}

	public function add(string $name, string $value): ParserInterface
	{
		$this->views[$name] = $value;

		return $this;
	}

	public function exists(string $view): bool
	{
		return \array_key_exists($view, $this->views);
	}

	public function parse(string $view, array $data = []): string
	{
		return ($this->exists($view)) ? $this->_parse($this->views[$view], $data) : '';
	}

	public function parse_string(string $string, array $data = []): string
	{
		$path = $this->cacheFolder . '/' . md5($string);

		FS::file_put_contents($path, $string, FILE_APPEND | LOCK_EX);

		return $this->_parse($path, $data);
	}

	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	protected function _parse($template, $data)
	{
		if ($template === '') {
			return false;
		}

		$replace = [];

		foreach ($data as $key => $val) {
			$merge = is_array($val) ? $this->_parse_pair($key, $val, $template) : $this->_parse_single($key, (string) $val, $template);
			$replace = array_merge($replace, $merge);
		}

		unset($data);

		return strtr($template, $replace);
	}

	/**
	 * Parse a single key/value
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _parse_single(string $key, $val, $string): array
	{
		return [$this->delimiters[0] . $key . $this->delimiters[1] => (string) $val];
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a tag pair
	 *
	 * Parses tag pairs: {some_tag} string... {/some_tag}
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	protected function _parse_pair($variable, $data, $string): array
	{
		$replace = [];

		preg_match_all(
			'#' . preg_quote($this->delimiters[0] . $variable . $this->delimiters[1]) . '(.+?)' . preg_quote($this->delimiters[0] . '/' . $variable . $this->delimiters[1]) . '#s',
			$string,
			$matches,
			PREG_SET_ORDER
		);

		foreach ($matches as $match) {
			$str = '';
			foreach ($data as $row) {
				$temp = [];
				foreach ($row as $key => $val) {
					if (is_array($val)) {
						$pair = $this->_parse_pair($key, $val, $match[1]);
						if (!empty($pair)) {
							$temp = array_merge($temp, $pair);
						}

						continue;
					}

					$temp[$this->delimiters[0] . $key . $this->delimiters[1]] = $val;
				}

				$str .= strtr($match[1], $temp);
			}

			$replace[$match[0]] = $str;
		}

		return $replace;
	}
} /* end class */
