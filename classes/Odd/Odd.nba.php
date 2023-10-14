<?php
include('Odd/OddGains.nba.php');
include('Odd/OddComparativeGains.nba.php');
include('Odd/OddSuccess.nba.php');
include('Odd/OddComparativeSuccess.nba.php');
include('Odd/OddComparativeProbability.nba.php');
include('Odd/OddRecord.nba.php');

class Odd {
    public $oddSuccess;

    function __construct($body) {
        $this->body = $body;

        $this->oddGains = new OddGains($body);
		$this->oddComparativeGains = new OddComparativeGains($body);
		$this->oddSuccess = new OddSuccess($body);
		$this->oddComparativeSuccess = new OddComparativeSuccess($body);
		$this->oddComparativeProbability = new OddComparativeProbability($body);
		$this->oddRecord = new OddRecord($body);
    }

    function getNumMatches($todd) {
        $sql = 'select count(id) as c from matches where todd1=' . $todd . ' or todd2=' . $todd; // echo "Odd::getNumMatches $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $c = $row['c'];

        return $c;
    }

    function getWins($odd) {
        $sql = 'select count(id) as c from matches where (todd1=' . $odd . ' and winner=1) or (todd2=' . $odd . ' and winner=2)'; // echo "OddSuccess::getWins $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $wins = $row['c'];

        return $wins;
    }

    function getSuccess($odd) {
        $sql = 'select success from odds where odd=' . $odd; 
        $res = $this->body->db->query($sql);
        $num = $this->body->db->num_rows();

        if( $num) {
            $row = $res->fetch_assoc();
            $success = $row['success'];
        } else {
            $success = 0;
        }

        return $success;
    }

    function getSequence($odd, $date) {
        $sql = 'select seqWins, seqLosses from oddsRecord where odd=' . $odd . ' and date<"' . $date . '" order by date desc limit 1'; // echo "$sql<br>";
        $res = $this->body->db->query($sql);
        $num = $this->body->db->num_rows();
        
        if ($num) {
            $row = $res->fetch_assoc();
            $seqWins = $row['seqWins'];
            $seqLosses = $row['seqLosses'];
        } else {
            $seqWins = 0;
            $seqLosses = 0;
        }        

        return [$seqWins, $seqLosses];
    }

    function list() {
        $odds = [];
        $winnersListings = [];
        $winnersPercent = [];
    
        $sql = 'select odd from odds';
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
            $odd = $row['odd'];
            $odds[] = $odd;
        }
    
        asort($odds); 

        $oddsAll = '';
    
        foreach($odds as $odd) {

            $proEarnings = 0;
            $conEarnings = 0;
            $minEarnings = 0;
            $maxEarnings = 0;
    
            $oddDetail = '
            <div class="third-level-title grid1211111 grid-gap50">
                <div>Date</div>
                <div>Teams</div>
                <div>Odds</div>
                <div class="right">Pro odds</div>
                <div class="right">Against odds</div>
                <div class="right">MIN</div>
                <div class="right">MAX</div>
            </div>';
    
            $sql = 'select id from matches where final=1 and (todd1=' . $odd . ' or todd2=' . $odd . ') order by id'; // echo "Odd::list: $sql<br><br>";
            $res = $this->body->db->query($sql);
            while($row = $res->fetch_assoc()) {
                $id = $row['id'];
                
                $date = $this->body->match->get($id, 'date');
                $team1 = $this->body->match->get($id, 'team1');
                $team2 = $this->body->match->get($id, 'team2');
                $odd1 = $this->body->match->get($id, 'odd1');
                $odd2 = $this->body->match->get($id, 'odd2');
                $winner = $this->body->match->get($id, 'winner');
                $betMin1 = $this->body->match->get($id, 'betMin1');
                $betMin2 = $this->body->match->get($id, 'betMin2');
                $betMax1 = $this->body->match->get($id, 'betMax1');
                $betMax2 = $this->body->match->get($id, 'betMax2');
                
                $team1name = $this->body->team->get($team1, 'name');
                $team2name = $this->body->team->get($team2, 'name');

                $pro = (($odd1 < $odd2) && $winner == 1) || (($odd1 > $odd2) && $winner == 2);
                $con = (($odd1 < $odd2) && $winner == 2) || (($odd1 > $odd2) && $winner == 1);
    
                if($pro && $winner == 1) {
                    $proEarnings += $odd1 - 1;
                    $conEarnings -= 1;
                } 
                if($pro && $winner == 2) {
                    $proEarnings += $odd2 - 1;
                    $conEarnings -= 1;
                } 
                if($con && $winner == 1) {
                    $conEarnings += $odd1 - 1;
                    $proEarnings -= 1;
                } 
                if($con && $winner == 2) {
                    $conEarnings += $odd2 - 1;
                    $proEarnings -= 1;
                }

                if($betMin1 && $winner == 1) $minEarnings += $odd1 - 1;
                if($betMin2 && $winner == 2) $minEarnings += $odd2 - 1;
                if($betMin1 && $winner == 2) $minEarnings += -1;
                if($betMin2 && $winner == 1) $minEarnings += -1;
    
                if($betMax1 && $winner == 1) $maxEarnings += $betMax1 * ($odd1 - 1);
                if($betMax2 && $winner == 2) $maxEarnings += $betMax2 * ($odd2 - 1);
                if($betMax1 && $winner == 2) $maxEarnings += -$betMax1;
                if($betMax2 && $winner == 1) $maxEarnings += -$betMax2;

                $styleColor = $pro ? 'green' : 'red';
    
                $spanTeam1 = $winner == 1 ? 'bold' : '';
                $spanTeam2 = $winner == 2 ? 'bold' : '';
    
                $oddDetail .= '
                    <div class="list-detail grid1211111 grid-gap50" style="color:' . $styleColor . '">
                        <span>' . $date . '</span>
                        <span><span class="' . $spanTeam1 . '">' . $team1name . '</span>-<span class="' . $spanTeam2 . '">' . $team2name . '</span></span>
                        <span>' . $odd1 . '-' . $odd2 . '</span>
                        <span class="pro-balance right">' . number_format($proEarnings, 2) . '</span>
                        <span class="con-balance right">' . number_format($conEarnings, 2) . '</span>
                        <span class="min-balance right">' . number_format($minEarnings, 2) . '</span>
                        <span class="max-balance right">' . number_format($maxEarnings, 2) . '</span>
                    </div>';
            }
    
            $matches = $this->getNumMatches($odd);

            $proEarnings = number_format($proEarnings, 2);
            $conEarnings = number_format($conEarnings, 2);
    
            $proPercent = floor(100 * $proEarnings / $matches);
            $conPercent = floor(100 * $conEarnings / $matches);
    
            $stylePro = $proEarnings > 0 ? 'green' : 'red';
            $styleCon = $conEarnings > 0 ? 'green' : 'red';
    
            $formattedId = 'all-' . (10 * $odd);

            $listing = '
                <div class="second-level-title grid8 right grid-gap100" onClick="toggleVisibility(\'' . $formattedId . '\')">
                    <div class="left">' . number_format($odd / 10, 1) . '</div>
                    <div>' . $matches . '</div>
                    <div>' . $this->oddSuccess->getWins($odd) . '</div>
                    <div>' . $this->oddSuccess->get($odd) . '%</div>
                    <div class="' . $stylePro . '">' . $proEarnings . '</div>
                    <div class="' . $stylePro . '">' . $proPercent . '%</div>
                    <div class="' . $styleCon . '">' . $conEarnings . '</div>
                    <div class="' . $styleCon . '">' . $conPercent . '%</div>
                </div>
                <div id="' . $formattedId . '" style="display:none">
                    <div class="box">
                    ' . $oddDetail . '
                    </div>
                </div>';
    
            $oddsAll .= $listing;
            if($matches > 10 && ($proPercent > 0 || $conPercent > 0)) {
                $winnersPercent[] = ($proPercent > $conPercent) ? $proPercent : $conPercent;
                $winnersListings[] = $listing;
            }

        }

        arsort($winnersPercent);
    
        $oddsWinners = '';
    
        foreach ($winnersPercent as $key => $value) {
            $listing = $winnersListings[$key];
            $listing = str_replace('all-', 'winners-', $listing);
            $oddsWinners .= $listing;
        }
    
        $oddsHeading = '
        <div class="second-level-title bg-white grid8 right grid-gap100 py-5">
            <div class="left">Odd</div>
            <div>Matches</div>
            <div>Wins</div>
            <div>Success</div>
            <div class="grid-span2 center bg-green">Gains betting pro odds</div>
            <div class="grid-span2 center bg-red">Gains betting against odds</div>
        </div>';

        $html = '
        <div class="first-level-title" onClick="toggleVisibility(\'winners\')">WINNERS</div>
        <div id="winners" style="display:block">
            <div class="box">
        ' . $oddsHeading . '
        ' . $oddsWinners . '
            </div>
        </div>
        <div class="first-level-title" onClick="toggleVisibility(\'all\')">ALL</div>
        <div id="all" style="display:none">
            <div class="box">
        ' . $oddsHeading . '
        ' . $oddsAll . '
            </div>
        </div>';        

        return $html;
    }
}
?>