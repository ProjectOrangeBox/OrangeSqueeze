<?php

$helpers['exp:lowercase'] = function($options) {
	/*
	if (!$output = ci()->handlebars->cache($options)) {
		$output = strtolower($options['fn']($options['_this']));

		ci()->handlebars->cache($options,$output);
	}
	*/

	return strtolower($options['fn']($options['_this']));
};
