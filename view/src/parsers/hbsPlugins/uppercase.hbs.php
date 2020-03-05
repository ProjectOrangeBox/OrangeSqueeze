<?php

$helpers['exp:uppercase'] = function($options) {
	/*
	if (!$output = ci()->handlebars->cache($options)) {
		$output = strtoupper($options['fn']($options['_this']));

		ci()->handlebars->cache($options,$output);
	}
	*/

	return strtoupper($options['fn']($options['_this']));
};
