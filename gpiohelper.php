<?php

abstract class GPIOEvent {

	/** @var GPIO */
	public $io;

	/** @var Callable */
	protected $callback;

	public function __construct($io, $pin, $callback) {
		$this->io = $io;
		$this->pin = $pin;
		$this->callback = $callback;

		$this->check();
	}

	abstract public function check();

	public function trigger() {
		call_user_func($this->callback, $this);
	}
}

class GPIOEventPinStateChange extends GPIOEvent {

	public $state;
	public $old_state;

	public function check() {
		$this->old_state = $this->state;
		$this->state = $this->io->getPin($this->pin);
		return ($this->state != $this->old_state);
	}
}

class GPIOComponent {}

class GPIOSevenSegmentDisplay extends GPIOComponent {

	protected $pinout;
	protected $map = [
		0 => 'abcdef',
		1 => 'bc',
		2 => 'abged',
		3 => 'abgcd',
		4 => 'fbgc',
		5 => 'afgcd',
		6 => 'afgecd',
		7 => 'abc',
		8 => 'abcdefg',
		9 => 'afbgc',
		'e' => 'afged',
	];

	/** @var  GPIO */
	public $io;


	public function __construct(GPIO $io, $pinout) {
		$this->io = $io;
		$this->setPinout($pinout);
		$this->setDisplay('');
	}

	public function setPinout($pinout) {
		foreach($pinout as $pin) {
			$this->io->setDirection($pin, 'out');
		}
		$this->pinout = $pinout;
	}

	public function setMap($map) {
		$this->map = $map;
	}

	public function getMap() {
		return $this->map;
	}

	public function setValue($value) {
		if(isset($this->map[$value])) {
			$this->setDisplay($this->map[$value]);
		}
		elseif(isset($this->map['e'])) {
			$this->setDisplay($this->map['e']);
		}
	}

	public function setDisplay($segments) {
		$active_pins = [];
		foreach(str_split($segments) as $segment) {
			if(isset($this->pinout[$segment])) {
				$active_pins[$this->pinout[$segment]] = $this->pinout[$segment];
			}
		}
		foreach($this->pinout as $pin) {
			$this->io->setPin($pin, isset($active_pins[$pin]) ? 1 : 0);
		}
	}
}


class GPIO {
	const EXPORT_PATH = '/sys/class/gpio/gpiochip0/subsystem/export';
	const PIN_DIRECTION_PATH = '/sys/class/gpio/gpio%s/direction';
	const PIN_VALUE_PATH = '/sys/class/gpio/gpio%s/value';
	const PINS = [0, 1, 6, 7, /*8,*/ 12, 13, 14, 18, 19, 23, 26];

	const OUT = 'out';
	const IN = 'in';

	protected $events = [];
	protected $end = false;
	protected $components;

	public function __construct($pins = []) {
		foreach(self::PINS as $pin) {
			$fh = $this->openPinFile(self::EXPORT_PATH, $pin, 'w');
			fwrite($fh, $pin);
			fclose($fh);
		}

		foreach($pins as $pin => $pin_direction) {
			if($this->getDirection($pin) != $pin_direction) {
				$this->setDirection($pin, $pin_direction);
			}
		}
	}

	public function getPins() {
		return self::PINS;
	}

	protected function openPinFile($pin_path, $pin, $rw) {
		$pin_file = sprintf($pin_path, $pin);
		if(!$fh = fopen($pin_file, $rw)) {
			throw new Exception("Could not open pin file $pin_file for $rw on pin $pin");
		}
		return $fh;
	}

	public function setDirection($pin, $direction) {
		$fh = $this->openPinFile(self::PIN_DIRECTION_PATH, $pin, 'w');
		fwrite($fh, $direction);
		fclose($fh);
	}

	public function getDirection($pin) {
		$fh = $this->openPinFile(self::PIN_DIRECTION_PATH, $pin, 'r');
		$result = '';
		while(!feof($fh)) {
			$result .= fread($fh, 1024);
		}
		fclose($fh);
		return trim($result);
	}

	public function setPin($pin, $value) {
		$fh = $this->openPinFile(self::PIN_VALUE_PATH, $pin, 'w');
		fwrite($fh, $value);
		fclose($fh);
	}

	public function getPin($pin) {
		$fh = $this->openPinFile(self::PIN_VALUE_PATH, $pin, 'r');
		$result = '';
		while(!feof($fh)) {
			$result .= fread($fh, 1024);
		}
		fclose($fh);
		return intval($result);
	}

	public function onChange($pin, $callback) {
		$this->events[] = new GPIOEventPinStateChange($this, $pin, $callback);
	}

	public function addComponent($name, $component) {
		$this->components[$name] = $component;
	}

	public function __get($name) {
		if(isset($this->components[$name])) {
			return $this->components[$name];
		}
		return null;
	}

	public function end() {
		$this->end = true;
	}

	public function run() {
		$this->end = false;
		while(true) {
			$event = next($this->events);
			if(!$event) {
				$event = reset($this->events);
			}
			if(!$event) {
				break;
			}
			if($event->check()) {
				$event->trigger();
			}
			if($this->end) {
				break;
			}
		}
	}
}