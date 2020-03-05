<?php

$helpers['if_ne'] = function($value1,$value2,$options) {
	if ($value1 != $value2) {
		$return = $options['fn']();
	} elseif ($options['inverse'] instanceof \Closure) {
		$return = $options['inverse']();
	}

	return $return;
};
