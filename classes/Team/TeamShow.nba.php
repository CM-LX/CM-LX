<?php
    function teamShow($id, $body) {
        include_once('Team/TeamGetGains.nba.php');
        include_once('Team/TeamGetGainsMinMax.nba.php');

        $name = $body->team->getField($id, 'name');
        $matches = $body->team->getField($id, 'matches');
        $wins = $body->team->getField($id, 'wins');
        $success = $body->team->getField($id, 'success');
        
        $gains = teamGetGains($id, $body);
        $roi = round(100 * $gains[0] / $matches);

        $data = teamGetGainsMinMax($id, $body, 'Min');
        $matchesMin = $data['matches'];
        $winsMin = $data['wins'];
        $gainsMin = $data['gains'] - $matchesMin;
        $successMin = $data['success'];
        $roiMin = round(100 * $gainsMin / $matchesMin);

        $data = teamGetGainsMinMax($id, $body, 'Max');
        $matchesMax = $data['matches'];
        $winsMax = $data['wins'];
        $gainsMax = $data['gains'] - $matchesMax;
        $successMax = $data['success'];
        $roiMax = round(100 * $gainsMax / $matchesMax);

        if($body->cm) {
            $form = 'AVERIGUAR ISTO MUITO BEM<br>
            <form action="teams.nba.php" method="POST">
                <input type="hidden" name="id" value="' . $id . '">
                <input type="hidden" name="updateTeam" value="1">
                <input type="text" name="name" value="' . $name . '" id="team-name-input">
            </form>';
        } else {
            $form = strtoupper($name);
        }
        
        $body->setTitle('TEAMS: ' . $name);
        
        $html = 
    '			<div class="first-level-title no-pointer">

                <div class="grid py-5">
                    <span class="span2">' . $form . '</span>
                    <span class="span2">Matches</span>
                    <span class="span2">Wins</span>
                    <span class="span2">Success</span>
                    <span class="span2">Gains</span>
                    <span class="span2">ROI</span>
                </div>	

                <div class="grid py-5">
                    <span class="span2"></span>
                    <span class="span2">' . $matches . '</span>
                    <span class="span2">' . $wins . '</span>
                    <span class="span2">' . $success . '%</span>
                    <span class="span2">AVERIGUAR ' . number_format($gains[0], 2) . '</span>
                    <span class="span2">' . $roi . '%</span>
                </div>	

                <div class="grid right normal py-5">
                    <span class="span2 left">MIN bets</span>
                    <span class="span2">' . $matchesMin . '</span>
                    <span class="span2">' . $winsMin . '</span>
                    <span class="span2">' . $successMin . '%</span>
                    <span class="span2">' . number_format($gainsMin, 2) . '</span>
                    <span class="span2">' . $roiMin . '%</span>
                </div>	

                <div class="grid right normal py-5">
                    <span class="span2 left">MAX bets</span>
                    <span class="span2">' . $matchesMax . '</span>
                    <span class="span2">' . $winsMax . '</span>
                    <span class="span2">' . $successMax . '%</span>
                    <span class="span2">' . number_format($gainsMax, 2) . '</span>
                    <span class="span2">' . $roiMax . '%</span>
                </div>

            </div>'
    . $body->team->getHistory($id);

        return $html;
    }
?>