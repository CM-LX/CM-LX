<?php
class OddComparativeGains extends Odd {
    public $body;
    
    private $result;

    function __construct($body) {
		$this->body = $body;
    }

    function get($odd1, $odd2) {
        $bet = '';
        $bet1a = 0;
        $bet1b = 0;
        $bet2a = 0;
        $bet2b = 0;
        $max1 = 0;
        $max2 = 0;
        
        [$gainsAsPro1, $gainsAsCon1] = $this->body->odd->oddGains->get($odd1);
        [$gainsAsPro2, $gainsAsCon2] = $this->body->odd->oddGains->get($odd2);

        $sumGains1 = $gainsAsPro1 + $gainsAsCon2;
        $sumGains2 = $gainsAsPro2 + $gainsAsCon1;

        $matches1 = $this->body->odd->getNumMatches($odd1);
        $matches2 = $this->body->odd->getNumMatches($odd2);

        $gainsPerMatch1 = ($gainsAsPro1 + $gainsAsCon2) / $matches1;
        $gainsPerMatch2 = ($gainsAsPro2 + $gainsAsCon1) / $matches2;

        $value1 = round(100 * $gainsPerMatch1);
        $value2 = round(100 * $gainsPerMatch2);

        // $value1 = floor(100 * $sumGains1);
        // $value2 = floor(100 * $sumGains2);

        if ($value1 > $value2) {
            $bet1a = 1;
            $bet1b = 1;
            $max1 = $bet1b;
            $bet = 'Bet ' . $bet1a . ':' . $bet1b . ' on 1';
        } elseif ($value2 > $value1) {
            $bet2a = 1;
            $bet2b = 1;
            $max2 = $bet2b;
            $bet = 'Bet ' . $bet2a . ':' . $bet2b . ' on 2';
        }

        $this->result['gainsAsProOdd1'] = number_format($gainsAsPro1, 2);
        $this->result['gainsAsProOdd2'] = number_format($gainsAsPro2, 2);
        $this->result['gainsAsConOdd1'] = number_format($gainsAsCon1, 2);
        $this->result['gainsAsConOdd2'] = number_format($gainsAsCon2, 2);
        $this->result['sumGains1'] = number_format($sumGains1, 2);
        $this->result['sumGains2'] = number_format($sumGains2, 2);
        $this->result['gainsPerMatch1'] = number_format($gainsPerMatch1, 2);
        $this->result['gainsPerMatch2'] = number_format($gainsPerMatch2, 2);
        $this->result['value1'] = $value1;
        $this->result['value2'] = $value2; 
        $this->result['bet1a'] = $bet1a;
        $this->result['bet1b'] = $bet1b;
        $this->result['bet2a'] = $bet2a;
        $this->result['bet2b'] = $bet2b;
        $this->result['max1'] = $max1;
        $this->result['max2'] = $max2;
        $this->result['bet'] = $bet;
        
        return $this->result;
    }

    function html() {
        $data = [];

        $line = ['Gains betting on this odd', $this->result['gainsAsProOdd1'], $this->result['gainsAsProOdd2']];
        $data[] = $line;
        $line = ['Gains betting against this odd', $this->result['gainsAsConOdd1'], $this->result['gainsAsConOdd2']];
        $data[] = $line;
        $line = ['Sum gains', $this->result['sumGains1'], $this->result['sumGains2']];
        $data[] = $line;
        $line = ['Gains per match', $this->result['gainsPerMatch1'], $this->result['gainsPerMatch2']];
        $data[] = $line;
        $line = ['Value', $this->result['value1'], $this->result['value2']];
        $data[] = $line;   

        return $data;
    }
}
?>