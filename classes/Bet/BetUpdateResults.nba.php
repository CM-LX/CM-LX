<?php
    function betUpdateResults($result, $body) {
        $body->bet->sum1 += $result['value1'];
        $body->bet->sum2 += $result['value2'];
    }
?>