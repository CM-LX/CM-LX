<?php
    function teamGetComparativeProbability($team1, $team2, $date, $body) {
        $team1Success = $body->team->getField($team1, 'success');
        $team2Success = $body->team->getField($team2, 'success');

        [ $seqWinsTeam1, $seqLossesTeam1 ] = $body->team->getSequence($team1, $date); 
        [ $seqWinsTeam2, $seqLossesTeam2 ] = $body->team->getSequence($team2, $date);

        if ($seqWinsTeam1) {
            $probLossTeam1 = $seqWinsTeam1 * (100 - $team1Success);
            $probWinTeam1 = 100 - $probLossTeam1;
        } else {
            $probWinTeam1 = $seqLossesTeam1 * $team1Success;
            $probLossTeam1 = $probWinTeam1 ? 100 - $probWinTeam1 : 0;
        }    
        
        if ($seqWinsTeam2) {
            $probLossTeam2 = $seqWinsTeam2 * (100 - $team2Success);
            $probWinTeam2 = 100 - $probLossTeam2;
        } else {
            $probWinTeam2 = $seqLossesTeam2 * $team2Success;
            $probLossTeam2 = $probWinTeam2 ? 100 - $probWinTeam2 : 0;
        }    
            
        if ($team1Success && $team2Success) {
            $sumProbWinTeam1 = $probWinTeam1 + $probLossTeam2;
            $sumProbWinTeam2 = $probWinTeam2 + $probLossTeam1;
        } else {
            $sumProbWinTeam1 = $team1Success ? $probWinTeam1 : 0;
            $sumProbWinTeam2 = $team2Success ? $probWinTeam2 : 0;
        }

        if ($sumProbWinTeam1 < 0) {
            $value1 = 100;
            $value2 = ($sumProbWinTeam2 - $sumProbWinTeam1);
        } elseif ($sumProbWinTeam2 < 0) {
            $value1 = $sumProbWinTeam1 - $sumProbWinTeam2;
            $value2 = 100;
        } else {
            $value1 = $sumProbWinTeam1;
            $value2 = $sumProbWinTeam2;
        }
        
        $result['team1Success'] = $team1Success;
        $result['team2Success'] = $team2Success;
        $result['seqWinsTeam1'] = $seqWinsTeam1;
        $result['seqWinsTeam2'] = $seqWinsTeam2;
        $result['seqLossesTeam1'] = $seqLossesTeam1;
        $result['seqLossesTeam2'] = $seqLossesTeam2;
        $result['probWinTeam1'] = $probWinTeam1;
        $result['probWinTeam2'] = $probWinTeam2;
        $result['probLossTeam1'] = $probLossTeam1;
        $result['probLossTeam2'] = $probLossTeam2;
        $result['sumProbWinTeam1'] = $sumProbWinTeam1;
        $result['sumProbWinTeam2'] = $sumProbWinTeam2;
        $result['value1'] = $value1;
        $result['value2'] = $value2;
        
        $data = [];

        $data[] = ['Success', $result['team1Success'], $result['team2Success']];
        $data[] = ['Consecutive wins', $result['seqWinsTeam1'], $result['seqWinsTeam2']];
        $data[] = ['Consecutive losses', $result['seqLossesTeam1'], $result['seqLossesTeam2']];
        $data[] = ['Win probability', $result['probWinTeam1'], $result['probWinTeam2']];
        $data[] = ['Loss probability', $result['probLossTeam1'], $result['probLossTeam2']];
        $data[] = ['Sum win probability', $result['sumProbWinTeam1'], $result['sumProbWinTeam2']];
        $data[] = ['Value', $result['value1'], $result['value2']];

        $result['data'] = $data;

        return $result;
    }
?>