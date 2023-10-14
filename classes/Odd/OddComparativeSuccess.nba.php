<?php
class OddComparativeSuccess extends Odd {
    public $body;

    private $result;

    function __construct($body) {
        $this->body = $body;
    }

    function get($odd1, $odd2) {
        $this->result['value1'] = $this->getSuccess($odd1);
        $this->result['value2'] = $this->getSuccess($odd2);
        
        return $this->result;
    }

    function html() {
        $data = [];

        $line = ['Success', $this->result['value1'], $this->result['value2']];
        $data[] = $line;
        $line = ['Value', $this->result['value1'], $this->result['value2']];
        $data[] = $line;

        return $data;
    }
}
?>