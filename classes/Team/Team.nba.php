<?php
// **********************************************************
// ** TOP LEVEL INCLUDE, USED EVERYWHERE: DON'T TOUCH THIS **
// **********************************************************
include('Team/TeamGetField.nba.php');

// *********************************************************
// VWEIFICAR SE ESTES INCLUDES TÊM DE FICAR AQUI
// *********************************************************

// include('Team/TeamRecord.nba.php');

class Team {
	public $body;

	function __construct($body) {
		$this->body = $body;
    }
	
	function buildGains() {
		include_once('Team/TeamBuildGains.nba.php');
		teamBuildGains($this->body);
	}
	
	function buildMatches() {
		include_once('Team/TeamBuildMatches.nba.php');
		teamBuildMatches($this->body);
	}

	function buildWins() {
		include_once('Team/TeamBuildWins.nba.php');
		teamBuildWins($this->body);
	}
	
	function create($name) {
		include_once('Team/TeamCreate.nba.php');
		teamCreate($name, $this->body);
	}
	
	function delete($id) {
		include_once('Team/TeamDelete.nba.php');
		teamDelete($id, $this->body);
	}
	
	function getComparativeGains($team1, $team2) { 
		include_once('Team/TeamGetComparativeGains.nba.php');
		return teamGetComparativeGains($team1, $team2, $this->body);
	}
	
	function getComparativeProbability($team1, $team2, $date) { 
		include_once('Team/TeamGetComparativeProbability.nba.php');
		return teamGetComparativeProbability($team1, $team2, $date, $this->body);
	}
	
	function getComparativeSuccess($team1, $team2) { 
		include_once('Team/TeamGetComparativeSuccess.nba.php');
		return teamGetComparativeSuccess($team1, $team2, $this->body);
	}
	
	function getField($id, $field) { 
		return teamGetField($id, $field, $this->body);
	}

	function getGains($id) { 
		include_once('Team/TeamGetGains.nba.php');
		return teamGetGains($id, $this->body);
	}
	
	function getHistory($id) {
		include_once('Team/TeamGetHistory.nba.php');
		return teamGetHistory($id, $this->body);
	}
	
	function getHistoryByDate($id, $date, $titleLevel) {
		include_once('Team/TeamGetHistoryByDate.nba.php');
		return teamGetHistoryByDate($id, $date, $titleLevel, $this->body);
	}
	
	function getMatchesByMonth($id, $month) {
		include_once('Team/TeamGetMatchesByMonth.nba.php');
		return teamGetMatchesByMonth($id, $month, $this->body);
	}
	
	function getNumMatches($id) { 
		include_once('Team/TeamGetNumMatches.nba.php');
		return teamGetNumMatches($id, $this->body);
    }	
	
	function getSelectList($id = 0) {
		include_once('Team/TeamGetSelectList.nba.php');
		return teamGetSelectList($id, $this->body);
	}	
	
	function getSequence($id, $date) {
		include_once('Team/TeamGetSequence.nba.php');
		return teamGetSequence($id, $date, $this->body);
    }	
	
	function listTeams() {
		include_once('Team/TeamListTeams.nba.php');
		return teamListTeams($this->body);
    }	
	
	function show($id) {
		include_once('Team/TeamShow.nba.php');
		return teamShow($id, $this->body);
	}

	function setGains($id) {
		include_once('Team/TeamSetGains.nba.php');
		teamSetGains($id, $this->body);
	}	

	function setName($id, $name) {
		include_once('Team/TeamSetName.nba.php');
		teamSetName($id, $name, $this->body);
	}	

	function setMatches($id) {
		include_once('Team/TeamSetMatches.nba.php');
		teamSetMatches($id, $this->body);
	}	

	function setRecord($id, $date) {
		include_once('Team/TeamSetRecord.nba.php');
		teamSetRecord($id, $date, $this->body);
	}	

	function setSuccess($id) {
		include_once('Team/TeamSetSuccess.nba.php');
		teamSetSuccess($id, $this->body);
	}	

	function setWins($id) {
		include_once('Team/TeamSetWins.nba.php');
		teamSetWins($id, $this->body);
	}	
}
?>