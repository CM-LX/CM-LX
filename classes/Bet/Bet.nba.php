<?php
include('Bet/BetListBets.nba.php');
include('Bet/BetListBet.nba.php');
include('Bet/BetShow.nba.php');
include('Bet/BetCalculate.nba.php');
include('Bet/BetUpdateResults.nba.php');
include('Bet/BetBuild.nba.php');

class Bet {
    public $body;

    public $date;
    public $team1;
    public $team2;
    public $odd1;
    public $odd2;
    public $todd1;
    public $todd2;
    public $winner;
    public $final;

    public $sum1 = 0;
    public $sum2 = 0;

    public $betMin1;
    public $betMin2;
    public $betMax1;
    public $betMax2;

    public $betData = [];

    function __construct($body) {
        $this->body = $body;
        // $this->build();
    }    

    function build() {
        return betBuild($this->body);
    }
    
    function calculate($id) {
        return betCalculate($id, $this->body);
    }

    function updateResults($result) {
        return betUpdateResults($result, $this->body);
    }
}
?>