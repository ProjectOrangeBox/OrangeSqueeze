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

namespace projectorangebox\handlebars_parser;

use FS;

class HandlebarsPluginCacher
{
	protected $plugins;
	protected $cacheFolder = '/var';
	protected $forceCompile = DEBUG;
	protected $pluginFiles = [];

	public function __construct(array &$config)
	{
		$this->cacheFolder = $config['cache folder'] ?? $this->cacheFolder;
		$this->forceCompile = $config['forceCompile'] ?? $this->forceCompile;
		$this->pluginFiles = $config['helper files'] ?? $this->pluginFiles;

		$cacheFile = $this->cacheFolder . '/cached.helpers.php';

		if ($this->forceCompile || !FS::file_exists($cacheFile)) {
			$combined  = '<?php' . PHP_EOL . '/*' . PHP_EOL . 'DO NOT MODIFY THIS FILE' . PHP_EOL . 'Written: ' . date('Y-m-d H:i:s T') . PHP_EOL . '*/' . PHP_EOL . PHP_EOL;

			/* find all of the plugin "services" */
			if (\is_array($this->pluginFiles)) {
				foreach ($this->pluginFiles as $path) {
					$pluginSource  = php_strip_whitespace(FS::resolve($path));
					$pluginSource  = trim(str_replace(['<?php', '<?', '?>'], '', $pluginSource));
					$pluginSource  = trim('/* ' . $path . ' */' . PHP_EOL . $pluginSource) . PHP_EOL . PHP_EOL;

					$combined .= $pluginSource;
				}
			}

			/* save to the cache folder on this machine (in a multi-machine env each will just recreate this locally) */
			FS::file_put_contents($cacheFile, trim($combined));
		}

		/* start with empty array */
		$helpers = [];

		/* include the combined "cache" file */
		include FS::resolve($cacheFile);

		$this->plugins = $helpers;
	}

	public function get()
	{
		return $this->plugins;
	}
} /* end class */
