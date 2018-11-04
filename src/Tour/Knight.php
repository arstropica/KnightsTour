<?php
namespace Tour;

class Knight
{

    /**
     *
     * @var Board
     */
    protected $board;
    
    /**
     *
     * @var array
     */
    protected static $movelist = [
        [1,2],
        [1,-2],
        [2,1],
        [2,-1],
        [-1,2],
        [-1,-2],
        [-2,1],
        [-2,-1]
    ];

    /**
     *
     * @var array
     */
    protected $moves = [];

    /**
     *
     * @var array
     */
    protected $history = [];

    /**
     *
     * @var array
     */
    protected $coverage = [];

    /**
     * Point of most returns
     *
     * @var array
     */
    protected $pmr;

    /**
     * Constructor
     *
     * @param Board $board            
     */
    
    /**
     * Debug setting
     *
     * @var integer
     */
    private $debug = 0;

    public function __construct(Board $board)
    {
        $this->board = $board;
        $this->moves[] = $this->history[] = $board->getPosition();
    }
    
    /**
     * Get adjacent move positions
     * 
     * @param integer $position
     * @param Board $board
     * @param boolean $unvisited
     * @return number[]
     */
    public function adjacent($position = null, $board = null, $unvisited = false) 
    {
        $adjacent = [];
        if (! isset($board)) $board = $this->board;
        if (! isset($position)) $position = $board->getPosition();
        $map = $board->getMap();
        $size = $board->getSize();
        foreach (self::$movelist as $xy) {
            $c = $board->positionCoords($position);
            $delta = [$c[0] + $xy[0], $c[1] + $xy[1]];
            if ($board->valid($delta)){
                $index = ($xy[1] * $size) + $xy[0] + $position;
                if((!$unvisited || $map[$index] === 0)) {
                    $adjacent[] = $index;
                }
            }
        }
        if ($this->debug) {
            print "Found adjacent indexes to {$position} : (" . implode(", ",$adjacent) . ").\n"; 
        }
        return $adjacent;
    }
    
    /**
     * Returns the number of (unvisited) squares in adjacent positions
     *
     * @param integer $position
     * @param Board $board
     * @param boolean $unvisited
     *
     * @return integer
     */
    public function accessibility($position, $board = null, $unvisited = true)
    {
        if (! isset($board)) $board = $this->board;
        return count($this->adjacent($position, $board, $unvisited));
    }
    
    /**
     * Scout neighboring squares and return accessibility
     *
     * @param integer $p
     * @param Board $board
     * @param boolean $unvisited
     *
     * @return number[]
     */
    public function scout($position = null, $board = null, $unvisited = true)
    {
        $accessibility = [];
        $board = $board ?: $this->board;
        if (!isset($position)) $position = $this->board->getPosition();
        $adjacent = $this->adjacent($position, $board, $unvisited);
        foreach ($adjacent as $move) {
            $accessibility[$move] = $this->accessibility($move, $board, $unvisited);
        }
        return $accessibility;
    }
    
    /**
     * Processes a single move
     *
     * @param integer $position
     * 
     * @return boolean
     */
    public function move($position)
    {
        $adjacent = $this->adjacent();
        if ($this->board->valid($position) && in_array($position, $adjacent)) {
            if ($this->board->update($position)) {
                $position = $this->board->getPosition();
                $this->history[] = $position;
                if ($this->debug) {
                    print "Move #" . (count($this->moves) + 1) . ": The Knight has moved to: " .  strtoupper(implode("", $this->board->getPosition('algebraic'))) . " [" . implode(",", $this->board->getPosition('numeric')) . "] (" . $position . ").\n";
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * Initiates the tour using Warnsdorff's Algorithm
     *
     * @param number $limit
     * @param boolean $closed
     *
     * @return boolean completed tour
     * @link http://www.geeksforgeeks.org/warnsdorffs-algorithm-knights-tour-problem/
     * @todo Look for prediction pattern for identifying closed tours
     */
    public function explore($limit = 64, $closed = true) {
        $board = $this->board;
        
        // Check unvisited squares
        if (pow($board->getSize(),2) == count(array_filter($board->getMap()))) {
            return true;
        }
        
        // Check iteration limit
        if ($limit <= 0) {
            return false;
        }
        
        // 3.a Let S be the set of positions accessible from P
        $this->coverage[] = $accessible_squares = $this->scout();
        $number_of_squares = count($accessible_squares);
        if ($this->debug) {
            print "Possible moves from [" . implode(",", $board->getPosition('numeric')) . "] : {$number_of_squares}.\n";
        }
        if ($number_of_squares > 0) {
            // 3b. Set P to be the position in S with minimum accessibility
            $minimum_accessibile_squares = array_keys($accessible_squares, min($accessible_squares));
            if (count($minimum_accessibile_squares) > 1) {
                // Randomize for decision forks  
                shuffle($minimum_accessibile_squares);
            }
            $minimum_accessibile_square = reset($minimum_accessibile_squares);
            $maximum_accessible_square = array_search(max($accessible_squares), $accessible_squares);
            // 3c. Mark the board at P with the current move number
            if ($this->move($minimum_accessibile_square)) {
                $this->moves[] = $minimum_accessibile_square;
            }
            if (empty($this->pmr) || $this->pmr['value'] <= $maximum_accessible_square) {
                $this->pmr = [
                    'index' => count($this->moves) - 1,
                    'value' => $maximum_accessible_square
                ];
            }
            if ($limit > 0) {
                return $this->explore(-- $limit);
            }
        } elseif ($this->pmr) {
            if ($this->debug) {
                print "Backtracking  ... .\n";
            }
            if ($this->_backtrack($this->pmr['index'], -- $limit, false)) {
                return $this->explore(-- $limit);
            }
        } else {
            if ($this->debug) {
                print "Backtracking  ... .\n";
            }
            if ($this->_backtrack(0, -- $limit, true)) {
                return $this->explore(-- $limit);
            }
        }
        return false;
    }
    
    /**
     * Check if tour is closed (Can end at starting point)
     * 
     * @return boolean
     */
    function closed()
    {
        $closed = false;
        $board = $this->board;
        if (pow($board->getSize(),2) == count(array_filter($board->getMap()))) {
            $adjacent = $this->adjacent($board->getPosition());
            if ($this->debug) {
                print "Looking for: [" . implode(",", $board->positionCoords($this->history[0])) . "] in neighboring positions: (" . implode(", ", array_map(function($n) use ($board) {return "[".implode(",", $board->positionCoords($n))."]";}, $adjacent)) . ")\n";
            }
            $closed = in_array($this->history[0], $adjacent);
        }
        return $closed;
    }
    
    /**
     * Performs a simple brute-force backtrack of previous moves, with optional check
     *
     * @param number $point            
     * @param number $limit            
     * @param string $survey            
     * @throws \Exception
     *
     * @todo Refactor to allow calculation of unexplored spaces
     *      
     * @return boolean
     */
    protected function _backtrack($point = 0, $limit = 0, $survey = false)
    {
        for ($i = count($this->moves) - 2; $i > $point; $i --) {
            $position = $this->moves[$i];
            if (! $this->move($position)) {
                throw new \Exception('Could not backtrack to move #' . $i);
                return false;
            } else {
                $this->moves[] = $position;
            }
            if ($survey) {
                if ($this->adjacent(null, null, true)) {
                    $this->pmr = null;
                    return true;
                }
            }
            if ($limit && (0 === -- $limit)) {
                return false;
            }
        }
        $this->pmr = null;
        return true;
    }

    /**
     * Get number of total moves
     *
     * @return number
     */
    public function getNumMoves()
    {
        return count($this->moves);
    }

    /**
     *
     * @return array $moves
     */
    public function getMoves()
    {
        return $this->moves;
    }

    /**
     *
     * @return Array $history
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     *
     * @return Array $coverage
     */
    public function getCoverage()
    {
        return $this->coverage;
    }
}

?>