<?php
class BetShow extends Bet {
    public $body;
    
    function __construct($body) {
        $this->body = $body;
    }

    function updateResults($result) {
        $this->sum1 += $result['value1'];
        $this->sum2 += $result['value2'];
    }

    function html($title, $data, $id = 0, $toggle = 1) {
        if($toggle) {
            $visibility = ' onClick="toggleVisibility(\'' . $title[0] . $id . '\')"';
            $pointer = ' pointer';
            $hidden = ' class="hidden" style="display:none"';
        } else {
            $visibility = '';
            $pointer = ' no-pointer';
            $hidden = '';
        }
        $html = '
        <div class="second-level-title grid' . $pointer . '"'. $visibility . '>
            <span class="span4 left">' . $title[0] . '</span>
            <span class="span3"></span>
            <span class="span2"></span>
            <span>' . $title[1] . '</span>
            <span class="span2">' . $title[2] . '</span>
        </div>';

        if(isset($title[3])) {
            $title3_1 = $title[3] ? $title[3] . '1' : '';
            $title3_2 = $title[3] ? $title[3] . '2' : '';
            $html .= '
                <div id="' . $title[0] . $id . '"' . $hidden . '>';

            if($title[3] != null) {
            $html .= '
                <div class="third-level-title grid">
                <span class="span4"></span>
                <span class="span2">' . $title3_1 . '</span>
                <span class="span2">' . $title3_2 . '</span>
                <span class="span2">Team1</span>
                <span class="span2">Team2</span>
                </div>';
            }
    
            foreach($data as $item) {
                $html .= 
    '            <div class="list-detail grid">
                    <span class="span4 left">' . $item[0] . '</span>
                    <span class="span2">' . $item[1] . '</span>
                    <span class="span2">' . $item[2] . '</span>';
                if(isset($item[3])) $html .= 
    '               <span class="span2">' . $item[3] . '</span>
                    <span class="span2">' . $item[4] . '</span>';
                $html .=
    '          </div>';
            }
    
            $html .= 
    '        </div>';
        }

        return $html;
    }

    function printSection($params, $id) {
        $result = $params['result'];
        $title = $params['title'];
        $data = $params['fn'];

        $this->updateResults($result);

        $line = ['<b>Totals</b>', '', '', '<b>' . $this->sum1 . '</b>', '<b>' . $this->sum2 . '</b>'];
        $data[] = $line;

        if($this->body->cm) return $this->html($title, $data, $id);
        
        return;
    }

    function show($id) {
        $this->betData = $this->body->bet->calculate($id);

        $this->sum1 = 0;
        $this->sum2 = 0;

        $html = '';
    
        $matchStyle = '';
        $team1Style = '';
        $team2Style = '';


        $date = $this->betData['date'];
        $team1 = $this->betData['team1'];
        $team2 = $this->betData['team2'];
        $odd1 = $this->betData['odd1'];
        $odd2 = $this->betData['odd2'];
        $todd1 = $this->betData['todd1'];
        $todd2 = $this->betData['todd2'];
        $final = $this->betData['final'];
        $winner = $this->betData['winner'];

        $team1Name = $this->body->team->getField($team1, 'name');
        $team2Name = $this->body->team->getField($team2, 'name');

        if($final) {
            $matchStyle = (($odd1 < $odd2) && $winner == 1) || (($odd2 < $odd1) && $winner == 2) ? 'green' : 'red';
            $team1Style = $winner == 1 ? 'bold' : '';
            $team2Style = $winner == 2 ? 'bold' : '';
        } else {
            $matchStyle = 'black';
        }

        $html .= 
'                <div class="first-level-title f-sb no-pointer ' . $matchStyle . '" style="font-weight:normal">
                    <span>' . $date . '</span>
                    <span><span class="' . $team1Style . '">' . $team1Name . '</span> - <span class="' . $team2Style . '">' . $team2Name . '</span></span>
                    <span>Odds: ' . $odd1 . ' - ' . $odd2 . '</span>
                </div>';

         $params['result'] = $this->body->odd->oddComparativeSuccess->get($todd1, $todd2);
        $params['title'] = ['Odd Success', $params['result']['value1'], $params['result']['value2'], 'Odd'];
        $params['fn'] = $this->body->odd->oddComparativeSuccess->html();
        $html .= $this->printSection($params, $id);

        $params['result'] = $this->body->odd->oddComparativeProbability->get($todd1, $todd2, $date);
        $params['title'] = ['Odd Probability', $params['result']['value1'], $params['result']['value2'], 'Odd'];
        $params['fn'] = $this->body->odd->oddComparativeProbability->html();
        $html .= $this->printSection($params, $id);

        $params['result'] = $this->body->odd->oddComparativeGains->get($todd1, $todd2);
        $params['title'] = ['Odd Gains', $params['result']['value1'], $params['result']['value2'], 'Odd'];
        $params['fn'] = $this->body->odd->oddComparativeGains->html();
        $html .= $this->printSection($params, $id);

        $params['result'] = $this->body->team->getComparativeSuccess($team1, $team2);
        $params['title'] = ['Team Success', $params['result']['value1'], $params['result']['value2'], 'Team'];
        $html .= $this->printSection($params, $id);

        $params['result'] = $this->body->team->getComparativeProbability($team1, $team2, $date);
        $params['title'] = ['Team Probability', $params['result']['value1'], $params['result']['value2'], 'Team'];
        $html .= $this->printSection($params, $id);

        $params['result'] = $this->body->team->getComparativeGains($team1, $team2);
        $params['title'] = ['Team Gains', $params['result']['value1'], $params['result']['value2'], 'Team'];
        $html .= $this->printSection($params, $id);

        $title = ['TOTALS', $this->sum1, $this->sum2];
        $data = [];
        if($this->body->cm) $html .=  $this->html($title, $data, $id);    
        
        if($this->sum1 < 0) $this->sum1 = $this->sum2 / 2;
        if($this->sum2 < 0) $this->sum2 = $this->sum1 / 2;

        $finalBetMin1 = $this->sum1 > $this->sum2 ? 1 : 0;
        $finalBetMin2 = $this->sum2 > $this->sum1 ? 1 : 0;

        $finalBetMax1 = floor($this->sum1 / $this->sum2);
        $finalBetMax2 = floor($this->sum2 / $this->sum1);

        $title = ['BETS', 'Team1', 'Team2', ''];
        $line = ['<b>MIN</b>', '', '', '<b>' . $finalBetMin1 . '</b>', '<b>' . $finalBetMin2 . '</b>'];
        $data[] = $line;
        $line = ['<b>MAX</b>', '', '', '<b>' . $finalBetMax1 . '</b>', '<b>' . $finalBetMax2 . '</b>'];
        $data[] = $line;
        $html .=  $this->html($title, $data, $id, 0);     

        if($final) {
            $data = [];

            $gainsMin1 = 0;
            $gainsMin2 = 0;
            $gainsMax1 = 0;
            $gainsMax2 = 0;

            $gainsMin1 = $finalBetMin1 ? ($winner == 1 ? $odd1 - 1 : -1) : 0;
            $gainsMin2 = $finalBetMin2 ? ($winner == 2 ? $odd2 - 1 : -1) : 0;
            $gainsMax1 = $finalBetMax1 ? ($winner == 1 ? $finalBetMax1 * ($odd1 - 1) : -$finalBetMax1) : 0;
            $gainsMax2 = $finalBetMax2 ? ($winner == 2 ? $finalBetMax2 * ($odd2 - 1) : -$finalBetMax2) : 0;
    
            $title = ['GAINS', '', '', null];
            $line = ['<b>MIN</b>', '', '', '<b>' . $gainsMin1 . '</b>', '<b>' . $gainsMin2 . '</b>'];
            $data[] = $line;
            $line = ['<b>MAX</b>', '', '', '<b>' . $gainsMax1 . '</b>', '<b>' . $gainsMax2 . '</b>'];
            $data[] = $line;
            if($this->body->cm) $html .=  $this->html($title, $data, $id, 1);     
        }

        return $html;
    }
}
?>