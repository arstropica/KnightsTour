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
    protected $mdict = [
        "NN" => - 2,
        "EN" => - 1,
        "ES" => + 1,
        "SS" => + 2,
        "WS" => + 1,
        "WN" => - 1
    ];

    /**
     *
     * @var array
     */
    protected $movelist = [
        - 4 => 'NNE',
        - 3 => 'ENE',
        - 2 => 'ESE',
        - 1 => 'SSE',
        1 => 'NNW',
        2 => 'WNW',
        3 => 'WSW',
        4 => 'SSW'
    ];

    /**
     *
     * @var array
     */
    protected $compass = [
        - 4 => 'N',
        - 3 => 'W',
        - 2 => 'NW',
        - 1 => 'NE',
        1 => 'SW',
        2 => 'SE',
        3 => 'E',
        4 => 'S'
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
        $this->history[] = $board->location();
    }

    /**
     * Returns displacement for local moves
     *
     * @param string $bearing            
     * @return number[]|mixed[]
     */
    protected function _displace_local($bearing)
    {
        $bearing = strtoupper($bearing);
        $displacement = [];
        $points = preg_split('/^([nsew]{2})/i', $bearing, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (preg_grep("/" . $bearing . "/i", $this->movelist)) {
            $displacement[0] = $this->mdict[$points[0]];
            switch ($points[1]) {
                case 'E':
                    $displacement[1] = abs($displacement[0]) > 1 ? + 1 : + 2;
                    break;
                
                case 'W':
                    $displacement[1] = abs($displacement[0]) > 1 ? - 1 : - 2;
                    break;
            }
        }
        return $displacement;
    }

    /**
     * Returns displacement for extended moves
     *
     * @todo Figure out how to calculate for movement along cardinal points
     *      
     * @param string $bearing            
     * @param number $distance            
     */
    public function _displace_extended($bearing, $distance = 1)
    {
        $bearing = strtoupper($bearing);
        $displacement = [];
        if (preg_grep("/" . $bearing . "/i", $this->compass)) {}
    }

    /**
     * Performs a simple brute-force backtrack of previous moves, with optional check
     *
     * @param number $point            
     * @param string $survey            
     * @throws \Exception
     *
     * @todo Refactor to allow calculation of unexplored spaces
     *      
     * @return boolean
     */
    protected function _backtrack($point = 0, $survey = false)
    {
        for ($i = count($this->moves) - 1; $i > $point; $i --) {
            $bearing = $this->moves[$i];
            $reverse_bearing = $this->movelist[0 - array_search($bearing, $this->movelist)];
            if (! $this->move($reverse_bearing, true)) {
                throw new \Exception('Could not backtrack to move #' . $i);
                return false;
            } else {
                $this->moves[] = $reverse_bearing;
            }
            if ($survey) {
                if ($this->survey()) {
                    $this->pmr = null;
                    return true;
                }
            }
        }
        $this->pmr = null;
        return true;
    }

    /**
     * Processes a single move
     *
     * @param string $bearing            
     * @param boolean $force            
     * @return boolean
     */
    public function move($bearing, $force = false)
    {
        $displacement = $this->_displace_local($bearing);
        if ($displacement && $this->board->isValidMove($displacement)) {
            if ($this->board->update($displacement, $force)) {
                $this->history[] = $this->board->location();
                if ($this->debug) {
                    print "Move #" . count($this->moves) . ": The Knight has moved to: \"" . implode(", ", $this->board->location()) . "\".\n";
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Returns array of bearings for possible (unexplored) moves
     *
     * @param Board $board            
     * @param string $only_new            
     * @return number[]
     */
    public function survey(Board $board = null, $only_new = true)
    {
        $possibles = [];
        $board = $board ?: $this->board;
        foreach ($this->movelist as $bearing) {
            $displacement = $this->_displace_local($bearing);
            if ($board->isValidMove($displacement, $only_new)) {
                $possible = $board->check($displacement);
                if ($possible) {
                    $possibles[$bearing] = 1;
                }
            }
        }
        return $possibles;
    }

    /**
     * Initiates the tour
     *
     * @param number $limit            
     */
    public function explore($limit = 64)
    {
        if ($this->board->getCounter() == pow($this->board->getSize(), 2)) {
            return;
        }
        $current_location = $this->board->location();
        $bearings = $this->survey();
        $possibilities = count($bearings);
        if ($this->debug) {
            print "Possible moves: {$possibilities}.\n";
        }
        if ($possibilities) {
            foreach (array_keys($bearings) as $bearing) {
                $displacement = $this->_displace_local($bearing);
                $board = new Board($current_location, $this->board->getSize());
                if ($board->update($displacement)) {
                    if ($this->survey($board)) {
                        unset($board);
                        if ($this->move($bearing)) {
                            $this->moves[] = $bearing;
                        }
                        if (empty($this->pmr) || $this->pmr['value'] <= $possibilities) {
                            $this->pmr = [
                                'index' => count($this->moves) - 1,
                                'value' => $possibilities
                            ];
                        }
                        if ($limit > 0) {
                            return $this->explore(-- $limit);
                        }
                        break;
                    }
                }
            }
        } elseif ($this->pmr) {
            if ($this->debug) {
                print "Backtracking  ... .\n";
            }
            if ($this->_backtrack(0, true)) {
                return $this->explore(-- $limit);
            }
        }
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
}

?>