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

namespace projectorangebox\parser\handlebars;

use FS;
use Exception;
use LightnCandy\LightnCandy;
use projectorangebox\view\ParserInterface;
use projectorangebox\common\exceptions\io\FileNotFoundException;
use projectorangebox\parser\handlebars\exceptions\HandlebarsException;
use projectorangebox\view\exceptions\PartialNotFoundException;
use projectorangebox\view\exceptions\TemplateNotFoundException;

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

	protected $cacheFolder = '/var/cache/handlebars';
	protected $templates = [];
	protected $partials = [];
	protected $forceCompile = DEBUG;
	protected $HBCachePrefix = 'hbs.';
	protected $delimiters = ['{{', '}}'];
	protected $helpers = [];

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

		$this->cacheFolder = $config['cache folder'] ?? $this->cacheFolder;
		$this->templates = $config['templates'] ?? $this->templates;
		$this->partials = $config['partials'] ?? $this->partials;
		$this->forceCompile = $config['forceCompile'] ?? $this->forceCompile;
		$this->HBCachePrefix = $config['HBCachePrefix'] ?? $this->HBCachePrefix;
		$this->delimiters = $config['delimiters'] ?? $this->delimiters;
		$this->helpers = $config['helpers'] ?? $this->helpers; /* array of helpers */

		/* lightncandy handlebars compiler flags https://github.com/zordius/lightncandy#compile-options */
		$this->flags = $config['flags'] ?? LightnCandy::FLAG_ERROR_EXCEPTION | LightnCandy::FLAG_HANDLEBARS | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_RUNTIMEPARTIAL; /* integer */

		FS::mkdir($this->cacheFolder);
	}

	public function exists(string $view): bool
	{
		return \array_key_exists($view, $this->templates);
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

		/* Compile it into php magic! Thank you zordius https://github.com/zordius/lightncandy */
		return LightnCandy::compile($templateSource, [
			'flags' => $this->flags, /* compiler flags */
			'helpers' => $this->helpers, /* Add the plugins (handlebars.js calls helpers) */
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
						$template = '<!-- partial named "' . $name . '" could not be found --!>';
					}
				}

				return $template;
			},
		]);
	}

	/* add template is a path to a file */
	public function addTemplate(string $name, string $path): Handlebars
	{
		$this->templates[$name] = $path;

		return $this;
	}

	public function findTemplate(string $name): string
	{
		if (!isset($this->templates[$name])) {
			throw new TemplateNotFoundException($name);
		}

		return $this->templates[$name];
	}

	/* a partial is a string */
	public function addPartial(string $name, string $string): Handlebars
	{
		$this->partials[$name] = $string;

		return $this;
	}

	public function findPartial(string $name): string
	{
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
} /* end class */
