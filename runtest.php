<?php

include 'gpiohelper.php';

/*

This script toggles an LED 6 times when a button is pressed, then exits.

pin 0 - button
pin 1 - LED and resistor

 */

$io = new GPIO([0 => 'in', 1 => 'out']);

header('content-type: text/plain');

$io->onChange(0, function(GPIOEvent $event){
    static $count = 0;
    if($event->state == 1) {  // button is down
        $state = $event->io->getPin(1);
        $event->io->setPin(1, 1 - $state);
        $count ++;
        if ($count >= 6) {
            $event->io->end();
        }
    }
});

$io->run();


echo 'done';