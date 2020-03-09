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

namespace projectorangebox\simpleq;

use stdClass;
use Medoo\Medoo;
use projectorangebox\simpleq\exceptions\SimpleQException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class SimpleQ
{
	protected $table = 'simple_q';
	protected $statusMap = ['new' => 'N', 'tagged' => 'T', 'processed' => 'P', 'error' => 'E'];
	protected $statusMapFlipped;
	protected $cleanUpHours;
	protected $retagHours;
	protected $tokenHash;
	protected $jsonOptions = 0;

	protected $db; /* medoo */
	protected $queue = '';

	public function __construct(array $config = [])
	{
		$this->cleanUpHours = $config['clean up hours'] ?? 168; /* 7 days */
		$this->retagHours = $config['requeue hours'] ?? 1; /* 1 hour */

		/* internal */
		$this->statusMapFlipped = array_flip($this->statusMap);

		/* best to use these defaults */
		$this->tokenHash = $config['token hash'] ?? 'sha1'; /* sha1 */
		$this->jsonOptions = $config['json options'] ?? JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION;

		$this->db = $config['db'];

		if (!($this->db instanceof Medoo)) {
			throw new IncorrectInterfaceException('Meddo');
		}

		if (mt_rand(0, 99) < ($config['gc percent'] ?? 50)) {
			$this->cleanup();
		}
	}

	public function queue(string $queue): SimpleQ
	{
		$this->queue = $queue;

		return $this;
	}

	protected function getQueue()
	{
		if (empty($this->queue)) {
			throw new SimpleQException('Simple Q default queue not set.');
		}

		return md5($this->queue);
	}

	public function push($data, string $queue = null): bool
	{
		if ($queue !== null) {
			$this->queue($queue);
		}

		$columns = [
			'created' => date('Y-m-d H:i:s'),
			'status' => $this->statusMap['new'],
			'payload' => $this->encode($data),
			'queue' => $this->getQueue(),
			'token' => null
		];

		$stmt = $this->db->insert($this->table, $columns);

		return ($stmt) ? true : false;
	}

	public function pull($queue = null) /* record or false if nothing found */
	{
		$success = false;

		$token = hash($this->tokenHash, uniqid('', true));

		$set = [
			'token' => $token,
			'status' => $this->statusMap['tagged'],
			'updated' => date('Y-m-d H:i:s'),
		];

		$where = [
			'status' => $this->statusMap['new'],
			'token' => null,
			'queue' => $this->getQueue($queue),
			'LIMIT' => 1,
		];

		if ($this->db->update($this->table, $set, $where)->rowCount() > 0) {
			$record = $this->db->get($this->table, '*', ['token' => $token, 'LIMIT' => 1]);

			if (!$record) {
				throw new SimpleQException('Record could not be retrieved based on token.');
			}

			$record = (object) $record;

			$record->status_raw = $record->status;
			$record->status = $this->statusMapFlipped[$record->status];
			$record->payload = $this->decode($record);

			$success = new SimpleQrecord($record, $this);
		}

		return $success;
	}

	public function cleanup(): SimpleQ
	{
		/* reque tagged but "stuck?" */
		if ($this->retagHours > 0) {
			$this->db->update(
				$this->table,
				[
					'token' => null,
					'status' => $this->statusMap['new'],
					'updated' => date('Y-m-d H:i:s')
				],
				[
					'updated' => Medoo::raw('< now() - interval ' . (int) $this->retagHours . ' hour'),
					'status' => $this->statusMap['tagged']
				]
			);

			k($this->db->last());
		}

		/* clean out processed */
		if ($this->cleanUpHours > 0) {
			$this->db->delete($this->table, [
				'updated' => Medoo::raw('< now() - interval ' . (int) $this->cleanUpHours . ' hour'),
				'status' => $this->statusMap['processed'],
			]);

			k($this->db->last());
		}

		return $this;
	}

	/* internally used by simple q record */
	public function update($token, $status): bool
	{
		if (!array_key_exists($status, $this->statusMap)) {
			throw new SimpleQException('Unknown Simple Q record status "' . $status . '".');
		}

		return ($this->db->update($this->table, ['token' => null, 'updated' => date('Y-m-d H:i:s'), 'status' => $this->statusMap[$status]], ['token' => $token, 'LIMIT' => 1])) ? true : false;
	}

	public function complete(string $token): bool
	{
		return $this->update($token, 'processed');
	}

	public function new(string $token): bool
	{
		return $this->update($token, 'new');
	}

	public function error(string $token): bool
	{
		return $this->update($token, 'error');
	}

	/* protected */

	protected function encode($data): string
	{
		$payload = new stdClass;

		if (is_object($data)) {
			$payload->type = 'object';
		} elseif (is_scalar($data)) {
			$payload->type = 'scalar';
		} elseif (is_array($data)) {
			$payload->type = 'array';
		} else {
			throw new SimpleQException('Could not encode Simple Q data.');
		}

		$payload->data = $data;
		$payload->checksum = $this->createChecksum($data);

		return json_encode($payload, $this->jsonOptions);
	}

	protected function decode($record)
	{
		$payload_record = json_decode($record->payload, false);

		switch ($payload_record->type) {
			case 'object':
				$data = $payload_record->data;
				break;
			case 'array':
				$data = (array) $payload_record->data;
				break;
			case 'scalar':
				$data = $payload_record->data;
				break;
			default:
				throw new SimpleQException('Could not determine Simple Q data type.');
		}

		if (!$this->checkChecksum($payload_record->checksum, $data)) {
			throw new SimpleQException('Simple Q data checksum failed.');
		}

		return $data;
	}

	protected function createChecksum($payload)
	{
		return crc32(json_encode($payload, $this->jsonOptions));
	}

	protected function checkChecksum(string $checksum, $payload)
	{
		return ($this->createChecksum($payload) == $checksum);
	}
} /* end class */
