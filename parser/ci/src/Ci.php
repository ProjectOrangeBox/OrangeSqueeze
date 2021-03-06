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
 * Orginal Code
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 *
 */

namespace projectorangebox\parser\ci;

use FS;
use projectorangebox\view\ParserInterface;
use projectorangebox\view\ParserAbstract;

class Ci extends ParserAbstract implements ParserInterface
{
	public function _parse(string $__path, array $__data = []): string
	{
		return $this->_ci_parse(FS::file_get_contents($__path), $__data);
	}

	protected function _ci_parse($template, $data)
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
