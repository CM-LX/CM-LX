<?php
function betCalculate($id, $body) {
    $betData = [];

    $sum1 = 0;
    $sum2 = 0;

    $betData['date'] = $body->match->get($id, 'date');
    $betData['team1'] = $body->match->get($id, 'team1');
    $betData['team2'] = $body->match->get($id, 'team2');
    $betData['odd1'] = $body->match->get($id, 'odd1');
    $betData['odd2'] = $body->match->get($id, 'odd2');
    $betData['todd1'] = $body->match->get($id, 'todd1');
    $betData['todd2'] = $body->match->get($id, 'todd2');
    $betData['winner'] = $body->match->get($id, 'winner');
    $betData['final'] = $body->match->get($id, 'final');

    $body->bet->updateResults($body->odd->oddComparativeSuccess->get($betData['todd1'], $betData['todd2']));
    $body->bet->updateResults($body->odd->oddComparativeProbability->get($betData['todd1'], $betData['todd2'], $betData['date']));
    $body->bet->updateResults($body->odd->oddComparativeGains->get($betData['todd1'], $betData['todd2']));
    $body->bet->updateResults($body->team->getComparativeSuccess($betData['team1'], $betData['team2']));
    $body->bet->updateResults($body->team->getComparativeProbability($betData['team1'], $betData['team2'], $betData['date']));
    $body->bet->updateResults($body->team->getComparativeGains($betData['team1'], $betData['team2']));

    if($body->bet->sum1 < 0) $body->bet->sum1 = $body->bet->sum2 / 2;
    if($body->bet->sum2 < 0) $body->bet->sum2 = $body->bet->sum1 / 2;
    
    $betMin1 = $body->bet->sum1 > $body->bet->sum2 ? 1 : 0;
    $betMin2 = $body->bet->sum2 > $body->bet->sum1 ? 1 : 0;

    $betMax1 = floor($body->bet->sum1 / $body->bet->sum2);
    $betMax2 = floor($body->bet->sum2 / $body->bet->sum1);

    $betData['gainMin1'] = 0;
    $betData['gainMin2'] = 0;
    $betData['gainMax1'] = 0;
    $betData['gainMax2'] = 0;

    if($body->bet->final) {
        $betData['gainMin1'] = $betMin1 ? ($winner == 1 ? $odd1 - 1 : -1) : 0;
        $betData['gainMin2'] = $betMin2 ? ($winner == 2 ? $odd2 - 1 : -1) : 0;
        $betData['gainMax1'] = $betMax1 ? ($winner == 1 ? $betMax1 * ($odd1 - 1) : -$betMax1) : 0;
        $betData['gainMax2'] = $betMax2 ? ($winner == 2 ? $betMax2 * ($odd2 - 1) : -$betMax2) : 0;
    }
    
    $sql = 'update matches set betMin1=' . $betMin1 . ', betMin2=' . $betMin2 . ', betMax1=' . $betMax1 . ', betMax2=' . $betMax2 . ' where id=' . $id; // echo "BetCalculate::calculate $sql<br>";
    $body->db->query($sql);

    return $betData;
}
?>