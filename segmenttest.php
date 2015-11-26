<?php

include 'gpiohelper.php';

/*

This script cycles a 7-segment display through the numbers 0-9, then exits.

pin 0 - button
other pins - see declaration of GPIOSevenSegmentDisplay

 */

$io = new GPIO([0 => 'in', 1 => 'out']);

header('content-type: text/plain');

$io->addComponent('display', new GPIOSevenSegmentDisplay(
    $io,
    [
        'a' => 1,
        'b' => 23,
        'c' => 13,
        'd' => 14,
        'e' => 6,
        'f' => 7,
        'g' => 26,
    ]
));

$io->display->setValue(0);

$io->onChange(0, function(GPIOEvent $event) {
    static $count = 0;
    if($event->state == 1) {  // button is down
        $count ++;
        $event->io->display->setValue($count);
        if ($count >= 9) {
            $event->io->end();
        }
    }
});

$io->run();

echo 'done';