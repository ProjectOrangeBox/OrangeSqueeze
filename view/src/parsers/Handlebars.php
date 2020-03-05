<?php

namespace projectorangebox\view\parsers;

use FS;
use Exception;
use LightnCandy\LightnCandy;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\io\FileNotFoundException;
use projectorangebox\view\parsers\exceptions\HandlebarsException;
use projectorangebox\common\exceptions\mvc\PartialNotFoundException;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;

/**
 * Handlebars Parser
 *
 * This content is released under the MIT License (MIT)
 *
 * @package	CodeIgniter / Orange
 * @author	Don Myers
 * @author Zordius, Taipei, Taiwan
 * @license http://opensource.org/licenses/MIT MIT License
 * @link	https://github.com/ProjectOrangeBox
 * @link https://github.com/zordius/lightncandy
 *
 *
 *
 * Helpers:
 *
 * $helpers['foobar'] = function($options) {};
 *
 * $options =>
 * 	[name] => lex_lowercase # helper name
 * 	[hash] => Array # key value pair
 * 		[size] => 123
 * 		[fullname] => Don Myers
 * 	[contexts] => ... # full context as object
 * 	[_this] => Array # current loop context
 * 		[name] => John
 * 		[phone] => 933.1232
 * 		[age] => 21
 * 	['fn']($options['_this']) # if ??? - don't forget to send in the context
 * 	['inverse']($options['_this']) # else ???- don't forget to send in the context
 *
 */

class Handlebars implements ParserInterface
{
	protected $config = [];

	protected $plugins; /* actual plugins after loaded */

	protected $cacheFolder = '';
	protected $templates = [];
	protected $partials = [];
	protected $forceCompile = true;
	protected $HBCachePrefix = 'hbs.';
	protected $delimiters = ['{{', '}}'];

	/**
	 * Constructor - Sets Handlebars Preferences
	 *
	 * The constructor can be passed an array of config values
	 *
	 * @param	array	$userConfig = array()
	 */
	public function __construct(array &$config)
	{
		$this->config = &$config;

		$this->cacheFolder = $config['cache folder'] ?? '/var/cache/handlebars';
		$this->plugins = $config['plugins'] ?? [];
		$this->templates = $config['templates'] ?? [];
		$this->partials = $config['partials'] ?? [];
		$this->forceCompile = $config['forceCompile'] ?? DEBUG;
		$this->HBCachePrefix = $config['HBCachePrefix'] ?? 'hbs.';
		$this->delimiters = $config['delimiters'] ?? ['{{', '}}'];

		/* lightncandy handlebars compiler flags https://github.com/zordius/lightncandy#compile-options */
		$this->flags = $config['flags'] ?? LightnCandy::FLAG_ERROR_EXCEPTION | LightnCandy::FLAG_HANDLEBARS | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_RUNTIMEPARTIAL; /* integer */

		FS::mkdir($this->cacheFolder);
	}

	public function exists(string $name): bool
	{
		$name = strtolower(trim($name, '/'));

		\log_message('info', 'Find ' . $name);

		return isset($this->templates[$name]);
	}

	/* These are just like CodeIgniter regular parser */

	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse(string $templateFile, array $data = []): string
	{
		\log_message('info', 'handlebars parse ' . $templateFile);

		return $this->run($this->parseTemplate($templateFile, true), $data);
	}

	/**
	 * Parse a String
	 *
	 * Parses pseudo-variables contained in the specified string,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parseString(string $templateStr, array $data = []): string
	{
		\log_message('info', 'handlebars parse string ' . substr($templateStr, 0, 128) . '...');

		return $this->run($this->parseTemplate($templateStr, false), $data);
	}

	/*
	* set the template delimiters
	*
	* @param string/array
	* @param string
	* @return object (this)
	*/
	public function setDelimiters(/* string|array */$l = '{{', string $r = '}}'): ParserInterface
	{
		/* set delimiters */
		$this->delimiters = (is_array($l)) ? $l : [$l, $r];

		/* chain-able */
		return $this;
	}

	public function addView(string $name, string $value): ParserInterface
	{
		$this->addTemplate($name, $value);

		return $this;
	}

	/* handlebars library specific methods */

	/**
	 * heavy lifter - wrapper for lightncandy https://github.com/zordius/lightncandy handlebars compiler
	 *
	 * returns raw compiled_php as string or prepared (executable) php
	 *
	 * @param string
	 * @param string
	 * @param boolean
	 * @return string / closure
	 */
	public function compile(string $templateSource, string $comment = ''): string
	{
		\log_message('info', 'handlebars compiling');

		/* Get our helpers if they aren't already loaded */
		$this->loadHelpers();

		/* Compile it into php magic! Thank you zordius https://github.com/zordius/lightncandy */
		return LightnCandy::compile($templateSource, [
			'flags' => $this->flags, /* compiler flags */
			'helpers' => $this->plugins, /* Add the plugins (handlebars.js calls helpers) */
			'renderex' => '/* ' . $comment . ' compiled @ ' . date('Y-m-d h:i:s e') . ' */', /* Added to compiled PHP */
			'delimiters' => $this->delimiters,
			'partialresolver' => function ($context, $name) { /* partial & template handling */
				/* Try if it's a partial, template or insert as html comment */
				try {
					$template = $this->findPartial($name);
				} catch (Exception $e) {
					try {
						$template = FS::file_get_contents($this->findTemplate($name));
					} catch (Exception $e) {
						$template = '<!-- partial named "' . $name . '" could not found --!>';
					}
				}

				return $template;
			},
		]);
	}

	/* add template is a path to a file */
	public function addTemplate(string $name, string $path): Handlebars
	{
		$name = strtolower(trim($name, '/'));

		\log_message('info', 'handlebars add template ' . $name);

		$this->templates[$name] = '/' . trim($path, '/');

		return $this;
	}

	public function findTemplate(string $name): string
	{
		$name = strtolower(trim($name, '/'));

		\log_message('info', 'handlebars find template ' . $name);

		if (!isset($this->templates[$name])) {
			throw new TemplateNotFoundException($name);
		}

		return $this->templates[$name];
	}

	/* a partial is a string */
	public function addPartial(string $name, string $string): Handlebars
	{
		$name = strtolower(trim($name, '/'));

		\log_message('info', 'handlebars add partial ' . $name);

		$this->partials[$name] = $string;

		return $this;
	}

	public function findPartial(string $name): string
	{
		$name = strtolower(trim($name, '/'));

		\log_message('info', 'handlebars find partial ' . $name);

		if (!isset($this->partials[$name])) {
			throw new PartialNotFoundException($name);
		}

		return $this->partials[$name];
	}

	/*
	* save a compiled file
	*
	* @param string
	* @param string
	* @return boolean
	*/
	public function saveCompileFile(string $compiledFile, string $templatePhp): int
	{
		/* write out the compiled file */
		return FS::file_put_contents($compiledFile, '<?php ' . $templatePhp . '?>');
	}

	/**
	 * parseTemplate
	 *
	 * @param string $template
	 * @param bool $isFile
	 * @return void
	 */
	public function parseTemplate(string $template, bool $isFile): string
	{
		/* build the compiled file path */
		$compiledFile = $this->cacheFolder . '/' . $this->HBCachePrefix . md5($template) . '.php';

		/* always compile in development or not save or compile if doesn't exist */
		if ($this->forceCompile || !FS::file_exists($compiledFile)) {
			/* compile the template as either file or string */
			if ($isFile) {
				$source = FS::file_get_contents($this->findTemplate($template));
				$comment = $template;
			} else {
				$source = $template;
				$comment = 'parseString_' . md5($template);
			}

			$this->saveCompileFile($compiledFile, $this->compile($source, $comment));
		}

		return $compiledFile;
	}

	/**
	 * run
	 *
	 * @param string $compiledFile
	 * @param array $data
	 * @return void
	 */
	public function run(string $compiledFile, array $data): string
	{
		\log_message('info', 'handlebars run ' . $compiledFile);

		$compiledFile = FS::resolve($compiledFile);

		/* did we find this template? */
		if (!file_exists($compiledFile)) {
			/* nope! - fatal error! */
			throw new FileNotFoundException($compiledFile);
		}

		/* yes include it */
		$templatePHP = include $compiledFile;

		/* is what we loaded even executable? */
		if (!is_callable($templatePHP)) {
			throw new HandlebarsException();
		}

		/* send data into the magic void... */
		try {
			$output = $templatePHP($data);
		} catch (Exception $e) {
			echo '<h1>Handlebars Run Error:</h1><pre>';
			var_dump($e);
			\log_message('debug', \var_export($e, true));
			echo '</pre>';
			exit(1);
		}

		return $output;
	}

	/**
	 * loadHelpers
	 *
	 * @return void
	 */
	protected function loadHelpers(): void
	{
		\log_message('info', 'handlebars load helpers');

		$cacheFile = $this->cacheFolder . '/' . $this->HBCachePrefix . 'helpers.php';

		if ($this->forceCompile || !FS::file_exists($cacheFile)) {
			$combined  = '<?php' . PHP_EOL . '/*' . PHP_EOL . 'DO NOT MODIFY THIS FILE' . PHP_EOL . 'Written: ' . date('Y-m-d H:i:s T') . PHP_EOL . '*/' . PHP_EOL . PHP_EOL;

			/* find all of the plugin "services" */
			if (\is_array($this->plugins)) {
				foreach ($this->plugins as $path) {
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
		$plugin = [];

		/* include the combined "cache" file */
		include FS::resolve($cacheFile);

		$this->plugins = $plugin;
	}
} /* end class */
