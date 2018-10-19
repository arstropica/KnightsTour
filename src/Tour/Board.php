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
     * @var array
     */
    protected $loc;

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
     * @param array $loc            
     * @param number $size            
     */
    public function __construct($loc = [1,1], $size = 8)
    {
        $this->size = $size;
        $this->loc = $this->addressToNum($loc);
        $this->counter = 0;
        $map = array_fill(1, $size, array_fill(1, $size, 0));

        // 2. Mark the board at P with the move number “1”
        $map[$this->loc[1]][$this->loc[0]] = 1;
        $this->counter ++;
        $this->map = $map;
    }

    /**
     * Update board with new coordinates
     *
     * @param array $loc            
     * @return number|boolean
     */
    protected function _update($loc)
    {
        try {
            if (! $this->map[$loc[1]][$loc[0]]) {
                $this->map[$loc[1]][$loc[0]] = ++ $this->counter;
            }
            $this->loc = $loc;
            return $this->counter;
        } catch (\Exception $e) {
            print $e->getMessage() . "\n";
        }
        return false;
    }
    
    /**
     * Check validity / board status, and optionally update, displacement
     *
     * @param array $displacement            
     * @param string $update            
     * @param string $force            
     * @return number|boolean|number
     */
    protected function _check($displacement, $update = false, $force = false)
    {
        $result = 0;
        if (! $this->isValidMove($displacement)) {
            $result = - 1;
        }
        $loc = $this->loc;
        $loc[0] += $displacement[0];
        $loc[1] += $displacement[1];
        if ($this->map[$loc[1]][$loc[0]] === 0 || $force) {
            $result = 1;
        }
        
        if ($result === 1 && $update) {
            return $this->_update($loc);
        }
        return $result;
    }

    /**
     * Check validity and board status of a move displacement
     *
     * @param array $displacement            
     * @return number|boolean
     */
    public function check($displacement)
    {
        return $this->_check($displacement);
    }

    /**
     * Update board with new displacement
     *
     * @param array $displacement            
     * @param string $force            
     * @return number|boolean
     */
    public function update($displacement, $force = false)
    {
        return $this->_check($displacement, true, $force);
    }

    /**
     * Check validity of displacement
     *
     * @param array $displacement            
     * @param string $new            
     * @return boolean
     */
    public function isValidMove($displacement, $new = false)
    {
        $loc = $this->loc;
        if (! is_array($displacement)) {
            exit('Move not valid!');
        }
        foreach ($displacement as $axis => $squares) {
            $loc[$axis] += $displacement[$axis];
            if ($loc[$axis] > $this->size || $loc[$axis] < 1) {
                return false;
            }
        }
        if ($new && $this->map[$loc[1]][$loc[0]] === 1) {
            return false;
        }
        return true;
    }

    /**
     * Return current location on board
     *
     * @return number[]
     */
    public function location()
    {
        return [
            $this->loc[0],
            $this->loc[1]
        ];
    }
    
    /**
     * Returns a new location by displacement
     * 
     * @param array $location
     * @param array $displacement
     * @return array
     */
    public static function displace($location, $displacement)
    {
        return array_map(function($coord, $delta) {
            return $coord + $delta;
        }, $location, $displacement);
    }

    /**
     * Normalize alphanumeric address
     * 
     * @param array $loc
     * @return array normalized coordinates
     */
    public function addressToNum($loc)
    {
        return array_map(function($coord){
            return ctype_alpha($coord) ? ord($coord) - 96 : $coord;
        }, $loc);
    }
    
    /**
     * Returns alphanumeric location
     * 
     * @param array $loc
     * @return array
     */
    public function numToAddress($loc)
    {
        return [chr($loc[0] + 96), $loc[1]];
    }

    /**
     * Print Graphical representation of board
     *
     * @param string $graphical            
     * @return string
     */
    public function getMap($graphical = false)
    {
        $map = array_reverse($this->map, true);
        return $graphical ? implode("\n", array_map(function ($r) {
            return implode("\t", $r);
        }, $map)) : $map;
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
}

?>