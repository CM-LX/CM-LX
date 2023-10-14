<?php
class OddComparativeProbability extends Odd {
    public $body;
    
    private $result;

    function __construct($body) {
        $this->body = $body;
    }

    function get($odd1, $odd2, $date) {
        $odd1Success = $this->getSuccess($odd1);
        $odd2Success = $this->getSuccess($odd2);

        [ $seqWinsOdd1, $seqLossesOdd1 ] = $this->getSequence($odd1, $date);
        [ $seqWinsOdd2, $seqLossesOdd2 ] = $this->getSequence($odd2, $date);

        if ($seqWinsOdd1) {
            $probLossOdd1 = $seqWinsOdd1 * (100 - $odd1Success);
            $probWinOdd1 = 100 - $probLossOdd1;
        } else {
            $probWinOdd1 = $seqLossesOdd1 * $odd1Success;
            $probLossOdd1 = $probWinOdd1 ? 100 - $probWinOdd1 : 0;
        }    
        
        if ($seqWinsOdd2) {
            $probLossOdd2 = $seqWinsOdd2 * (100 - $odd2Success);
            $probWinOdd2 = 100 - $probLossOdd2;
        } else {
            $probWinOdd2 = $seqLossesOdd2 * $odd2Success;
            $probLossOdd2 = $probWinOdd2 ? 100 - $probWinOdd2 : 0;
        }    
            
        if ($odd1Success && $odd2Success) {
            $sumProbWinOdd1 = $probWinOdd1 + $probLossOdd2;
            $sumProbWinOdd2 = $probWinOdd2 + $probLossOdd1;
        } else {
            $sumProbWinOdd1 = $odd1Success ? $probWinOdd1 : 0;
            $sumProbWinOdd2 = $odd2Success ? $probWinOdd2 : 0;
        }

        if ($sumProbWinOdd1 < 0) {
            $value1 = 100;
            $value2 = ($sumProbWinOdd2 - $sumProbWinOdd1);
        } elseif ($sumProbWinOdd2 < 0) {
            $value1 = $sumProbWinOdd1 - $sumProbWinOdd2;
            $value2 = 100;
        } else {
            $value1 = $sumProbWinOdd1;
            $value2 = $sumProbWinOdd2;
        }

        $this->result['odd1Success'] = $odd1Success;
        $this->result['odd2Success'] = $odd2Success;
        $this->result['seqWinsOdd1'] = $seqWinsOdd1;
        $this->result['seqWinsOdd2'] = $seqWinsOdd2;
        $this->result['seqLossesOdd1'] = $seqLossesOdd1;
        $this->result['seqLossesOdd2'] = $seqLossesOdd2;
        $this->result['probWinOdd1'] = $probWinOdd1;
        $this->result['probWinOdd2'] = $probWinOdd2;
        $this->result['probLossOdd1'] = $probLossOdd1;
        $this->result['probLossOdd2'] = $probLossOdd2;
        $this->result['sumProbWinOdd1'] = $sumProbWinOdd1;
        $this->result['sumProbWinOdd2'] = $sumProbWinOdd2;
        $this->result['value1'] = $value1;
        $this->result['value2'] = $value2;
        
        return $this->result;
    }

    function html() {
        $data = [];

        $line = ['Success', $this->result['odd1Success'], $this->result['odd2Success']];
        $data[] = $line;
        $line = ['Consecutive wins', $this->result['seqWinsOdd1'], $this->result['seqWinsOdd2']];
        $data[] = $line;
        $line = ['Consecutive losses', $this->result['seqLossesOdd1'], $this->result['seqLossesOdd2']];
        $data[] = $line;
        $line = ['Win probability', $this->result['probWinOdd1'], $this->result['probWinOdd2']];
        $data[] = $line;
        $line = ['Loss probability', $this->result['probLossOdd1'], $this->result['probLossOdd2']];
        $data[] = $line;
        $line = ['Sum win probability', $this->result['sumProbWinOdd1'], $this->result['sumProbWinOdd2']];
        $data[] = $line;
        $line = ['Value', $this->result['value1'], $this->result['value2']];
        $data[] = $line;

        return $data;
    }
}
?>