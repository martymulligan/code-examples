<?php

class MedianHeap {
    /**
     * @var SplMinHeap $highHeap holds the larger numbers, smallest on top
     */
    protected $highHeap;
    /**
     * @var SplMaxHeap $lowHeap holds the smaller numbers, largest on top
     */
    protected $lowHeap;

    /**
     * MedianHeap constructor.
     * @param array $numbers an array containing the data set
     */
    public function __construct($numbers=null) {
        $this->highHeap = new SplMinHeap();
        $this->lowHeap = new SplMaxHeap();
        if(is_array($numbers)) {
            foreach($numbers as $num) $this->insert($num);
        }
    }

    /**
     * Add a number to the data set
     * @param $num
     * @throws Exception if the data provided is non numeric
     */
    public function insert($num) {
        if(!is_numeric($num)) {
            throw new Exception("Found non numeric data in set");
        }
        if($this->lowHeap->count() == 0 || $this->lowHeap->top() > $num) {
            $this->lowHeap->insert($num);
        }
        else {
            $this->highHeap->insert($num);
        }
        $this->rebalance();
    }

    /**
     * returns the difference of the # items in lowHeap minus the # in highHeap
     * @return int negative result means highHeap has more, positive means lowHeap has more
     */
    protected function lowHighDiff() {
        return $this->lowHeap->count() - $this->highHeap->count();
    }

    /**
     * Balance low and high heaps so they have roughly the same # of items
     */
    protected function rebalance() {
        $diff = $this->lowHighDiff();
        if($diff < -1) {
            $this->lowHeap->insert($this->highHeap->extract());
        }
        else if($diff > 1) {
            $this->highHeap->insert($this->lowHeap->extract());
        }
    }

    /**
     * Get the median of the current data set.
     * If an even # of items are in the set, the median is calculated by taking the mean of the 2 median items
     * @return float|int
     */
    public function getMedian() {
        $diff = $this->lowHighDiff();
        if($diff == 0) {
            $med = ($this->lowHeap->top() + $this->highHeap->top())/2;
        }
        else if($diff > 0) {
            $med = $this->lowHeap->top();
        }
        else {
            $med = $this->highHeap->top();
        }
        return $med;
    }
}

/**
 * Get the median from an array of numbers
 * @param array $numbers
 * @return float|int
 */
function median_array(array $numbers) {
    sort($numbers);
    if( count($numbers)%2 == 0) {
        $mid = (count($numbers) / 2) - 1;
        $med = ($numbers[$mid] + $numbers[$mid+1]) / 2;
    }
    else {
        $med = $numbers[floor(count($numbers)/2)];
    }
    return $med;
}


class Timer  {
    protected $start;

    public function __construct() {
        $this->reset();
    }

    public function getTime() {
        return microtime(true) - $this->start;
    }

    public function reset() {
        $this->start = microtime(true);
    }
}

/**
 * benchmarking
 */

$array_size = rand(100000, 900000);
$set = array();
for($i=0; $i < $array_size; $i++) {
    $set[] = rand(1,1000000);
}

//print_r($set);

$timer = new Timer();
$heap = new MedianHeap($set);
$heap_median = $heap->getMedian();
$heap_time = $timer->getTime();


$timer->reset();
$fxn_median = median_array($set);
$fxn_time = $timer->getTime();

$out = "Data set contains ".count($set)." elements\n"
    . $heap_time . " - MedianHeap answer: $heap_median "
    . ( ($heap_time < $fxn_time) ? '*' : '') . "\n"
    . $fxn_time . " - median_array answer: $fxn_median "
    . ( ($heap_time > $fxn_time) ? '*' : '') . "\n"
    . "* = faster\n";
echo $out;