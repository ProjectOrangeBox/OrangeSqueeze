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

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class exists extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* exists[model_name.column] */
		$this->error_string = '%s that you requested already exists.';

		list($model, $column) = explode('.', $options, 2);

		if (empty($model)) {
			return false;
		}

		if (empty($column)) {
			return false;
		}

		/* try to load the model */
		ci()->load->model($model);

		return (method_exists(ci()->$model, 'exists')) ? ci()->$model->exists($field, $column) : false;
	}
} /* end class */
