<?php

namespace projectorangebox\models;

use Medoo\Medoo;
use projectorangebox\models\DatabaseModel;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class MedooDatabaseModel extends DatabaseModel
{
	public function __construct(array $config = [])
	{
		$connection = $this->connection ?? 'default';

		parent::__construct(new \Medoo\Medoo([
			'database_type' => $config['connections'][$connection]['type'],
			'database_name' => $config['connections'][$connection]['name'],
			'server' => $config['connections'][$connection]['server'],
			'username' => $config['connections'][$connection]['username'],
			'password' => $config['connections'][$connection]['password'],
		]), $config);

		if (!($this->db instanceof Medoo)) {
			throw new IncorrectInterfaceException('Medoo');
		}
	}
}
