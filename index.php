<?php

use Tour\Board;
use Tour\Knight;

require_once dirname(__FILE__) . '/vendor/autoload.php';

$random = true;
$size = 8;
$output = true;
if (isset($argv[3])) {
    $size = $argv[3];
}

if (isset($argv[4])) {
    $output = $argv[4] ? true : false;
}

// 1. Set P to be a random initial position on the board
$loc = [rand(1, $size), rand(1, $size)];
if (isset($argv[1], $argv[2])) {
    $random = false;
    $loc = [$argv[1], $argv[2]];
}

$closed = false;
$counter = 0;
$kmoves = 0;
while (!$closed) {
    $counter ++;
    $board = new Board($loc, $size);
    $knight = new Knight($board);
    if ($output) {
        print "New Starting Position: " . strtoupper(implode("", $board->getPosition('algebraic'))) . " [" . implode(",", $loc) . "].\n";
    }
    try {
        $knight->explore(pow($size, 2) * 2);
        $closed = $knight->closed();
        $kmoves += $knight->getNumMoves();
        if ($random && ! $closed) {
            // 1. Set P to be a random initial position on the board
            $loc = [rand(1, $size), rand(1, $size)];
        } else {
            if ($knight->closed()) {
                if ($output) {
                    print "1 closed tour performed.\n";
                }
                if ($counter > 1) {
                    if ($output) {
                        print ($counter - 1) . " open tour(s) performed.\n";
                    }
                }
            } else {
                if ($output) {
                    print $counter . " open tour(s) performed.\n";
                }
            }
            if ($output) {
                print $board->getCounter() . " / " . pow($size, 2) . " squares covered.\n";
                print "Total Number of Calculated Moves: " . $kmoves . ".\n";
            }
            
            // 4. Return the marked board — each square will be marked with the move number on which it is visited
            if ($output) {
                echo print_r($board->getMap(true), true) . "\n";
            }
            break;
        }
    } catch (\Exception $e) {
        print $e->getMessage() . "\n";
        exit;
    }
    if ($output) {
        print "\n";
    }
}