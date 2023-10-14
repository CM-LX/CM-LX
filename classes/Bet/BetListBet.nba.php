<?php
class BetListBet extends Bet {
    public $body;

    function __construct($body) {
        $this->body = $body;
    }

    function formatLine($title, $result) {
        $value1 = $result['value1'];
        $value2 = $result['value2'];
        $this->sum1 += $value1;
        $this->sum2 += $value2;

        return '
        <div class="grid-bet-detail right">
            <div class="left">' . $title . '</div>
            <div>' . $value1 . '</div>
            <div>' . $value2 . '</div>
            <div>' . $this->sum1 . '</div>
            <div>' . $this->sum2 . '</div>
        </div>';         
    }

    function listBet($id) {
        $this->sum1 = 0;
        $this->sum2 = 0;
        
        $betDetail = '
        <div class="grid-bet-detail bold right">
            <div class="left">Value</div>
            <div>Team1</div>
            <div>Team2</div>
            <div>Sum1</div>
            <div>Sum2</div>
            <div>Gain</div>
            <div>Balance</div>
        </div>';
        
        $date = $this->body->match->get('date', $id);
        $team1 = $this->body->match->get('team1', $id);
        $team2 = $this->body->match->get('team2', $id);
        $odd1 = $this->body->match->get('odd1', $id);
        $odd2 = $this->body->match->get('odd2', $id);
        $todd1 = $this->body->match->get('todd1', $id);
        $todd2 = $this->body->match->get('todd2', $id);
        $winner = $this->body->match->get('winner', $id);
        
        $team1Name = $this->body->team->getName($team1);
        $team2Name = $this->body->team->getName($team2);
        
        $matchStyle = '';
        $matchStyle = (($odd1 < $odd2) && $winner == 1) || (($odd2 < $odd1) && $winner == 2) ? 'green' : 'red';
        $team1Style = $winner == 1 ? 'bold' : '';
        $team2Style = $winner == 2 ? 'bold' : '';

        $betDetail .= $this->formatLine('Odd success', $this->body->odd->oddComparativeSuccess->get($todd1, $todd2));
        $betDetail .= $this->formatLine('Odd probability', $this->body->odd->oddComparativeProbability->get($todd1, $todd2, $date));
        $betDetail .= $this->formatLine('Odd gains', $this->body->odd->oddComparativeGains->get($todd1, $todd2));
        $betDetail .= $this->formatLine('Team success', $this->body->team->teamComparativeSuccess->get($team1, $team2));
        $betDetail .= $this->formatLine('Team probability', $this->body->team->teamComparativeProbability->get($team1, $team2, $date));
        $betDetail .= $this->formatLine('Team gains', $this->body->team->teamComparativeGains->get($team1, $team2));

        
        // MIN

        $team1BetMin = $this->sum1 > $this->sum2 ? 1 : 0;
        $team2BetMin = $this->sum2 > $this->sum1 ? 1 : 0;

        $gainBetMin = (($team1BetMin && $winner == 1) || ($team2BetMin && $winner == 2)) ? ($team1BetMin * ($odd1 - 1)) + ($team2BetMin * ($odd2 - 1)) : -1;

        $this->body->betListBets->dailyMinBalance += $gainBetMin;

        $betStyle = $gainBetMin > 0 ? 'green' : 'red';

        $betDetail .= '
        <div class="grid-bet-detail right bold ' . $betStyle . '">
            <div class="left">Min (sum1 > sum2)</div>
            <div></div>
            <div></div>
            <div>' . $team1BetMin . '</div>
            <div>' . $team2BetMin . '</div>
            <div>' . number_format($gainBetMin, 2) . '</div>
            <div>' . number_format($this->body->betListBets->dailyMinBalance, 2) . '</div>
            </div>';

            
        // MAX

        if ($this->sum1 > 0 && $this->sum2 > 0) {
            $team1BetMax = floor($this->sum1 / $this->sum2);
            $team2BetMax = floor($this->sum2 / $this->sum1);
        } else {
            $team1BetMax = $this->sum1 > $this->sum2 ? 1 : 0;
            $team2BetMax = $this->sum2 > $this->sum1 ? 1 : 0;
        }

        $gainBetMax = (($team1BetMax && $winner == 1) || ($team2BetMax && $winner == 2)) ? ($team1BetMax * ($odd1 - 1)) + ($team2BetMax * ($odd2 - 1)) : -($team1BetMax+$team2BetMax);

        $this->body->betListBets->dailyMaxBalance += $gainBetMax;

        $betStyle = $gainBetMax > 0 ? 'green' : 'red';

        $betDetail .= '
                        <div class="grid-bet-detail right bold ' . $betStyle . '">
                            <div class="left">Max (sum1 / sum2)</div>
                            <div></div>
                            <div></div>
                            <div>' . $team1BetMax . '</div>
                            <div>' . $team2BetMax . '</div>
                            <div>' . number_format($gainBetMax, 2) . '</div>
                            <div>' . number_format($this->body->betListBets->dailyMaxBalance, 2) . '</div>
                        </div>';

        $html = '
                        <div class="list-detail grid211 grid-gap50 pointer" onClick="toggleVisibility(\'match' . $id . '\')">
                            <div class="list-detail-heading">
                                ' . $id . ': <span class="' . $team1Style . ' ' . $matchStyle . '">' . $team1Name . '</span>-<span class="' . $team2Style . ' ' . $matchStyle . '">' . $team2Name . '</span> (' . $odd1 . '-' . $odd2 . ')
                            </div>
                            <div class="list-detail-heading grid3">
                                <span></span>
                                <span class="right">' . number_format($gainBetMin, 2) . '</span>
                                <span class="right">' . number_format($gainBetMax, 2) . '</span>
                            </div>
                            <div class="list-detail-heading grid3">
                                <span></span>
                                <span class="right">' . number_format($this->body->betListBets->dailyMinBalance, 2) . '</span>
                                <span class="right">' . number_format($this->body->betListBets->dailyMaxBalance, 2) . '</span>
                            </div>
                        </div>
                        <div id="match' . $id . '" class="hidden bet-detail" style="display:none">
' . $betDetail . '
                        </div>';

        $sql = 'update matches set betMin1=' . $team1BetMin . ', betMin2=' . $team2BetMin . ', betMax1=' . $team1BetMax . ', betMax2=' . $team2BetMax . ' where id=' . $id; 
        $this->body->db->query($sql);

        return $html;    
    }
}
?>