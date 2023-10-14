<?php
    function teamGetComparativeGains($team1, $team2, $body) {
        
        [$gainsAsPro1, $gainsAsCon1] = $body->team->getGains($team1);
        [$gainsAsPro2, $gainsAsCon2] = $body->team->getGains($team2);

        $sumGains1 = $gainsAsPro1 + $gainsAsCon2;
        $sumGains2 = $gainsAsPro2 + $gainsAsCon1;

        $matches1 = $body->team->getNumMatches($team1);
        $matches2 = $body->team->getNumMatches($team2);

        $gainsPerMatch1 = ($gainsAsPro1 + $gainsAsCon2) / $matches1;
        $gainsPerMatch2 = ($gainsAsPro2 + $gainsAsCon1) / $matches2;

        $value1 = round(100 * $gainsPerMatch1);
        $value2 = round(100 * $gainsPerMatch2);

        $result['gainsAsProTeam1'] = number_format($gainsAsPro1, 2);
        $result['gainsAsProTeam2'] = number_format($gainsAsPro2, 2);
        $result['gainsAsConTeam1'] = number_format($gainsAsCon1, 2);
        $result['gainsAsConTeam2'] = number_format($gainsAsCon2, 2);
        $result['sumGains1'] = number_format($sumGains1, 2);
        $result['sumGains2'] = number_format($sumGains2, 2);
        $result['gainsPerMatch1'] = number_format($gainsPerMatch1, 2);
        $result['gainsPerMatch2'] = number_format($gainsPerMatch2, 2);
        $result['value1'] = $value1;
        $result['value2'] = $value2; 
    
        $data = [];
        
        $data[] = ['Gains betting on this team', $result['gainsAsProTeam1'], $result['gainsAsProTeam2']];
        $data[] = ['Gains betting against this team', $result['gainsAsConTeam1'], $result['gainsAsConTeam2']];
        $data[] = ['Sum gains', $result['sumGains1'], $result['sumGains2']];
        $data[] = ['Gains per match', $result['gainsPerMatch1'], $result['gainsPerMatch2']];
        $data[] = ['Value', $result['value1'], $result['value2']];  
        
        $result['data'] = $data;
        
        return $result;
    }
?>