<?php

use Tour\Board;
use Tour\Knight;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$size = 8;
if (isset($argv[3])) {
    $size = $argv[3];
}
$loc = [rand(1, $size), rand(1, $size)];
if (isset($argv[1], $argv[2])) {
    $loc = [$argv[1], $argv[2]];
}

$board = new Board($loc, $size);
$knight = new Knight($board);
print "Starting Position: " . implode(", ", $loc) . "\n";
try {
    $knight->explore(pow($size, 2) * 2);
    print $board->getCounter() . " / " . pow($size, 2) . " squares covered.\n";
    print "Total Number of Calculated Moves: " . $knight->getNumMoves() . ".\n";
    
    print_r($board->getMap(true));
} catch (\Exception $e) {
    print $e->getMessage() . "\n";
}
print "\n";
