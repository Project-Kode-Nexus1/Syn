<?php
declare(strict_types=1);

final class SynMachine {
	private bool $loaded = false; // Specifies if the machine is loaded

	private SynCpu $cpu;

	public function __construct() {
		$this->cpu = new SynCpu();
	}

	// Starts the machine, REQUIRED TO CALL BEFORE ANY OTHER FUNCTION
	public function start(array $configuration = []) : void {
		if ($this->loaded) {
			throw new RuntimeException("(SynMachine): Machine is already started!");
		}

		$this->cpu->reset();

		$this->loaded = true;
		echo "(SynMachine): Started!\n";
	}

	// Returns the state of the machine (if it has been started)
	public function is_started(): bool {
		return $this->loaded;
	}
}

require_once __DIR__ . '/Cpu/cpu.php';

$machine = new SynMachine();
if (!$machine->is_started()) {
	$machine->start();
}
?>