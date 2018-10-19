<?php

use Tour\Board;
use Tour\Knight;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$size = 8;
if (isset($argv[3])) {
    $size = $argv[3];
}

// 1. Set P to be a random initial position on the board
$loc = [rand(1, $size), rand(1, $size)];
if (isset($argv[1], $argv[2])) {
    $loc = [$argv[1], $argv[2]];
}

$result = false;
$counter = 0;
$kmoves = 0;
while (!$result) {
    $counter ++;
    $board = new Board($loc, $size);
    $knight = new Knight($board);
    print "New Starting Position: " . strtoupper(implode("", $board->numToAddress($loc))) . " [" . implode(",", $loc) . "].\n";
    try {
        $result = $knight->explore_warnsdorff(pow($size, 2) * 2);
        $kmoves += $knight->getNumMoves();
        if (! $result) {
            // 1. Set P to be a random initial position on the board
            $loc = [rand(1, $size), rand(1, $size)];
        } else {
            print $counter . " tour(s) performed.\n";
            print $board->getCounter() . " / " . pow($size, 2) . " squares covered.\n";
            print "Total Number of Calculated Moves: " . $kmoves . ".\n";
            
            // 4. Return the marked board — each square will be marked with the move number on which it is visited
            print_r($board->getMap(true));
        }
    } catch (\Exception $e) {
        print $e->getMessage() . "\n";
        exit;
    }
    print "\n";
}