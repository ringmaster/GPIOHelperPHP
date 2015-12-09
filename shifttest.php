<?php

include 'gpiohelper.php';


$io = new GPIO([
    0 => 'out',  // data
    1 => 'out',  // clock
    6 => 'out',  // latch
    7 => 'in',   // button
]);


function setLine($line){
    global $io;

    $bits = str_split($line, 1);
    foreach($bits as $bit) {
        $bit = intval($bit);
        $io->setPin(0, $bit);
        $io->setPin(1, 1);
        $io->setPin(1, 0);
    }
    $io->setPin(0, 0);
    $io->setPin(6, 1);
    $io->setPin(6, 0);
}

$lines = <<< LINES
00000001
00000010
00000100
00001000
00010000
00100000
01000000
10000000
00000000
10000000
01000000
00100000
10010000
01001000
00100100
00010010
00001001
00000100
00000010
00000001
10000000
01000000
00100000
00010000
10001000
01000100
00100010
00010001
00100010
01000100
10001000
00011000
00100100
01000010
10000001
00011000
00100100
01000010
10000001
00000000
11111111
00000000
11111111
00000000
11111111
10101010
01010101
10101010
01010101
00010001
00100010
01000100
10001000
01000100
00100010
00010001
01010101
01000100
10001000
01000100
00100010
00010001
00001000
00000100
00000010
00000001
00000000
10000000
01000000
00100000
10010000
01001000
00100100
10010010
01001001
00100100
00010010
00001001
00000100
00000010
00000001
00000000
1
0
0
0
0
0
0
0
0
0
LINES;
$lines = explode("\n", $lines);

$io->onClock(7, function(GPIOEventClock $event) use(&$lines) {
    if($line = next($lines)) {
        setLine($line);
    }
    else {
        reset($lines);
    }
    time_nanosleep(0, 100000000);
});

$io->onChange(7, function(GPIOEventPinStateChange $event) {
   $event->io->end();
});

$io->run();

echo 'done';
?>
<a href="/php/shifttest.php" style="padding:15px; background-color: #999999;color: black;border:1px solid #666666">Run Again</a>
