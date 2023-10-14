<?php
class BetCalculate extends Bet {
    public $body;
    
    function __construct($body) {
        $this->body = $body;
    }

    function updateResults($result) {
        $this->sum1 += $result['value1'];
        $this->sum2 += $result['value2'];
    }

    function calculate($id) {
        $this->betData = [];

        $this->sum1 = 0;
        $this->sum2 = 0;

        $this->betData['date'] = $this->body->match->get($id, 'date');
        $this->betData['team1'] = $this->body->match->get($id, 'team1');
        $this->betData['team2'] = $this->body->match->get($id, 'team2');
        $this->betData['odd1'] = $this->body->match->get($id, 'odd1');
        $this->betData['odd2'] = $this->body->match->get($id, 'odd2');
        $this->betData['todd1'] = $this->body->match->get($id, 'todd1');
        $this->betData['todd2'] = $this->body->match->get($id, 'todd2');
        $this->betData['winner'] = $this->body->match->get($id, 'winner');
        $this->betData['final'] = $this->body->match->get($id, 'final');

        $this->updateResults($this->body->odd->oddComparativeSuccess->get($this->betData['todd1'], $this->betData['todd2']));
        $this->updateResults($this->body->odd->oddComparativeProbability->get($this->betData['todd1'], $this->betData['todd2'], $this->betData['date']));
        $this->updateResults($this->body->odd->oddComparativeGains->get($this->betData['todd1'], $this->betData['todd2']));
        $this->updateResults($this->body->team->teamComparativeSuccess->getComparativeSuccess($this->betData['team1'], $this->betData['team2']));
        $this->updateResults($this->body->team->teamComparativeProbability->getComparativeProbability($this->betData['team1'], $this->betData['team2'], $this->betData['date']));
        $this->updateResults($this->body->team->teamComparativeGains->getComparativeGains($this->betData['team1'], $this->betData['team2']));

        if($this->sum1 < 0) $this->sum1 = $this->sum2 / 2;
        if($this->sum2 < 0) $this->sum2 = $this->sum1 / 2;
        
        $this->betMin1 = $this->sum1 > $this->sum2 ? 1 : 0;
        $this->betMin2 = $this->sum2 > $this->sum1 ? 1 : 0;

        $this->betMax1 = floor($this->sum1 / $this->sum2);
        $this->betMax2 = floor($this->sum2 / $this->sum1);

        $this->betData['gainMin1'] = 0;
        $this->betData['gainMin2'] = 0;
        $this->betData['gainMax1'] = 0;
        $this->betData['gainMax2'] = 0;

        if($this->final) {
            $this->betData['gainMin1'] = $this->betMin1 ? ($this->winner == 1 ? $this->odd1 - 1 : -1) : 0;
            $this->betData['gainMin2'] = $this->betMin2 ? ($this->winner == 2 ? $this->odd2 - 1 : -1) : 0;
            $this->betData['gainMax1'] = $this->betMax1 ? ($this->winner == 1 ? $this->betMax1 * ($this->odd1 - 1) : -$this->betMax1) : 0;
            $this->betData['gainMax2'] = $this->betMax2 ? ($this->winner == 2 ? $this->betMax2 * ($this->odd2 - 1) : -$this->betMax2) : 0;
        }
        
        $sql = 'update matches set betMin1=' . $this->betMin1 . ', betMin2=' . $this->betMin2 . ', betMax1=' . $this->betMax1 . ', betMax2=' . $this->betMax2 . ' where id=' . $id; echo "BetCalculate::calculate $sql<br>";
        $this->body->db->query($sql);

        return $this->betData;
    }
}
?>