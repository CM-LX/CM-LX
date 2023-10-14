<?php
    function teamGetComparativeSuccess($team1, $team2, $body) {
        $result['value1'] = $body->team->getField($team1, 'success');
        $result['value2'] = $body->team->getField($team2, 'success');

        $data = [];

        $data[] = ['Success', $result['value1'], $result['value2']];
        $data[] = ['Value', $result['value1'], $result['value2']];

        $result['data'] = $data;

        return $result;
    }
?>