<?php

namespace projectorangebox\data;

use Adbar\Dot;
use Exception;
use projectorangebox\session\SessionInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Data implements DataInterface
{
	protected $dot;
	protected $sessionService;
	protected $sessionKey = 'data::persist::';
	protected $persistenceSupported = false;

	public function __construct(array &$config)
	{
		$this->dot = new Dot;

		if (isset($config['sessionService'])) {
			$this->sessionService = $config['sessionService'];

			if (!($this->sessionService instanceof SessionInterface)) {
				throw new IncorrectInterfaceException('SessionInterface');
			}

			$this->persistenceSupported = true;

			$this->pullPersist();
		}
	}

	public function set($notations, $value = null, bool $persist = false): DataInterface
	{
		if (is_array($notations)) {
			$this->dot->set($notations);
		} else {
			$this->dot->set($notations, $value);
		}

		if ($persist) {
			$this->pushPersist($notations);
		}

		return $this;
	}

	public function all(): array
	{
		return $this->dot->all();
	}

	public function dot(): Dot
	{
		return $this->dot;
	}

	public function clear(string $notation): DataInterface
	{
		$this->dot->clear((array) $notation);

		return $this;
	}

	public function count(string $notation): int
	{
		return $this->dot->count($notation);
	}

	public function delete(string $notation): DataInterface
	{
		$this->dot->delete($notation);

		return $this;
	}

	public function get(string $notation, $default = null)
	{
		return $this->dot->get($notation, $default);
	}

	public function has($notations): bool
	{
		return $this->dot->has($notations);
	}

	public function isEmpty($notations = null): bool
	{
		return $this->dot->isEmpty($notations);
	}

	public function pull(string $notation, $default = null)
	{
		return $this->dot->pull($notation, $default);
	}

	public function push(string $notation, $value): DataInterface
	{
		$this->dot->push($notation, $value);

		return $this;
	}

	/* presistence */

	protected function pushPersist($notations): void
	{
		if ($this->persistenceSupported) {
			foreach ((array) $notations as $notation) {
				$this->sessionService->set($this->sessionKey . $notation, $this->dot->get($notation));
			}
		} else {
			throw new Exception('Session not supplied to Data so presistence is not supported.');
		}
	}

	protected function pullPersist(): void
	{
		if ($this->persistenceSupported) {
			$sessionKeyStrLen = strlen($this->sessionKey);

			foreach ($this->sessionService as $key => $context) {
				if (substr($key, 0, $sessionKeyStrLen) == $this->sessionKey) {
					$this->set(substr($key, $sessionKeyStrLen), $context);

					/* now delete the persistent record */
					$this->sessionService->delete($key);
				}
			}
		}
	}
} /* end class */
