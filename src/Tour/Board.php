<?php
namespace Tour;

class Board
{

    /**
     *
     * @var integer
     */
    protected $size;

    /**
     *
     * @var integer
     */
    protected $position;

    /**
     *
     * @var integer
     */
    protected $counter;

    /**
     *
     * @var array
     */
    protected $map;

    /**
     * Constructor
     *
     * @param array $c
     * @param number $size            
     */
    public function __construct($c = [1,1], $s = 8)
    {
        $this->map = array_fill(1, $s*$s, 0);
        $this->size = $s;
        $this->position = $this->positionIndex($c);
        if (! $this->valid($this->position)) {
            throw new \Exception('Invalid Position : '.$this->position);
            exit;
        }
        $this->counter = 0;

        // 2. Mark the board at P with the move number “1”
        $this->map[$this->position] = 1;
        $this->counter ++;
    }
    
    /**
     * Validate position exists on board
     * 
     * @param integer|array $position
     * @return boolean
     */
    public function valid($position) {
        if (is_array($position)) {
            $s = $this->size;
            return array_reduce($position, function($valid, $xy) use ($s){return $xy <= $s && $xy > 0 ? $valid : false;}, true);
        } else {
            return (array_key_exists($position, $this->map));
        }
    }
    
    /**
     * Update board with new coordinates
     *
     * @param integer $position            
     * @return number|boolean
     */
    public function update($position)
    {
        if (array_key_exists($position, $this->map)) {
            if (0 === $this->map[$position]) {
                $this->map[$position] = ++ $this->counter;
            }
            $this->position = $position;
            return $this->counter;
        }
        return false;
    }

    /**
     * Returns coordinates from indexed position
     * 
     * @param number $index
     * @return number[]
     */
    public function positionCoords($index)
    {
        $s = $this->size;
        return [(($index - 1) % $s) + 1, (intdiv($index - 1, $s)) + 1];
    }

    /**
     * Returns index of position coordinates
     * 
     * @param array $coords
     * @return number
     */
    public function positionIndex($coords) 
    {
        return intval(($coords[1] - 1) * $this->size) + intval(is_numeric($coords[0]) ? $coords[0] : ord($coords[0]) - 96);
    }
    
    /**
     * Print Graphical representation of board
     *
     * @param string $graphical            
     * @return string
     */
    public function getMap($graphical = false)
    {
        $map = $this->map;
        if ($graphical) {
            $map = [];
            $s = $this->size;
            for ($i=pow($s,2); $i > 0; $i -=$s) {
                $map[] = array_slice($this->map, $i - $s, $s, true);
            }
            return implode("\n", array_map(function($rank){return implode("\t", $rank);},$map));
        }
        return $map;
    }

    /**
     * Return hit counter
     *
     * @return number
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Return board size
     *
     * @return number
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Return board position
     * 
     * @param string $mode
     * 
     * @return number
     */
    public function getPosition($mode = 'index') {
        $p = $position = $this->position;
        $s = $this->size;
        switch ($mode) {
            case 'algebraic':
                $position = [chr(($p - 1) % $s + 97), (intdiv($p - 1, $s)) + 1];
                break;
            default:
            case 'numeric':
                $position = [(($p - 1) % $s) + 1, (intdiv($p - 1, $s)) + 1];
                break;
            case 'index' :
                $position = $p;
                break;
        }
        return $position;
    }
}

?>