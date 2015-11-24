<?php

class GPIOHelper {
	const EXPORT_PATH = '/sys/class/gpio/gpiochip0/subsystem/export';
	const PIN_DIRECTION_PATH = '/sys/class/gpio/gpio%s/direction';
	const PIN_VALUE_PATH = '/sys/class/gpio/gpio%s/value';
	const PINS = [0, 1, 6, 7, /*8,*/ 12, 13, 14, 23, 26, /*21, 20,*/ 19, 18];
	
	const OUT = 'out';
	const IN = 'in';

	public function __construct($pins = []) {
		foreach(self::PINS as $pin) {
			$fh = fopen(self::EXPORT_PATH, 'w');
			fwrite($fh, $pin);
			fclose($fh);
		}
		
		foreach($pins as $pin => $pin_direction) {
			$this->setDirection($pin, $direction);
		}
	}
	
	public function getPins() {
		return self::PINS;
	}
	
	public function setDirection($pin, $direction) {
		$fh = fopen(sprintf(self::PIN_DIRECTION_PATH, $pin), 'w');
		fwrite($fh, $direction);
		fclose($fh);
	}
	
	public function getDirection($pin) {
		if($fh = fopen(sprintf(self::PIN_DIRECTION_PATH, $pin), 'r')) {
			while(!feof($fh)) {
				$result .= fread($fh, 1024);
			}
			fclose($fh);
			return trim($result);
		}
		else {
			throw new Exception("Direction can't be obtained from pin $pin.");
		}
	}
	
	public function setPin($pin, $value) {
		$fh = fopen(sprintf(self::PIN_VALUE_PATH, $pin), 'w');
		fwrite($fh, $value);
		fclose($fh);
	}

	public function getPin($pin) {
		$fh = fopen(sprintf(self::PIN_VALUE_PATH, $pin), 'r');
		while(!feof($fh)) {
			$result .= fread($fh, 1024);
		}
		fclose($fh);
		return $result;
	}
}
