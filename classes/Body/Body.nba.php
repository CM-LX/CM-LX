<?php
include('Db/Db.nba.php');
include('Match/Match.nba.php');
include('Team/Team.nba.php');
include('Odd/Odd.nba.php');
include('Bet/Bet.nba.php');

class Body {
	public $cm;
	public $db;
	private $get_id;
	public $logout;
	private $name;

	private $arrowUp;
	private $arrowDown;

	public $title;
	public $html;
	
	public $team;
	private $createTeam;
	private $updateTeam;
	private $deleteTeam;
	
	public $match;
	private $createMatch;
	private $updateMatch;
	private $deleteMatch;

	public $odd;
	
	public $bet;
	private $betShow;
	private $betListBet;
	private $betListBets;
	
	function __construct() {
		$this->cm = isset($_COOKIE['ckcm']);

		$this->setVariables();
		
		$this->db = new Db;
		$this->match = new MyMatch($this);
		$this->team = new Team($this);
		$this->team->buildMatches();
		$this->odd = new Odd($this);
		$this->bet = new Bet($this);
		$this->betListBet = new BetListBet($this);
		$this->betListBets = new BetListBets($this);
		$this->betShow = new BetShow($this);

		$this->arrowUp = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="height:18px"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>';

		$this->arrowDown = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="height:18px"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>';

		$this->init();
	}

	function setVariables() {
		// GET
		$this->get_id 		= isset($_GET['id']) 			? intval($_GET['id']) 			: null;
		// $this->logout 		= isset($_GET['logout']) 		? 1 							: null;
		$this->deleteTeam	= isset($_GET['deleteTeam'])	? intval($_GET['deleteTeam'])	: null;
		$this->deleteMatch	= isset($_GET['deleteMatch'])	? intval($_GET['deleteMatch'])	: null;
		// POST
		$this->post_id 		= isset($_POST['id']) 			? intval($_POST['id']) 			: null;
		$this->date 		= isset($_POST['date']) 		? strip_tags($_POST['date']) 	: null;
		$this->team1 		= isset($_POST['team1']) 		? intval($_POST['team1']) 		: null;
		$this->team2 		= isset($_POST['team2']) 		? intval($_POST['team2']) 		: null;
		$this->odd1 		= isset($_POST['odd1']) 		? floatval($_POST['odd1']) 		: null;
		$this->odd2 		= isset($_POST['odd2']) 		? floatval($_POST['odd2']) 		: null;
		$this->winner 		= isset($_POST['winner']) 		? intval($_POST['winner']) 		: null;		
		$this->name 		= isset($_POST['name']) 		? strip_tags($_POST['name']) 	: null;
		$this->createTeam 	= isset($_POST['createTeam']) 	? 1 							: null;
		$this->updateTeam 	= isset($_POST['updateTeam']) 	? 1 							: null;
		$this->createMatch 	= isset($_POST['createMatch']) 	? 1 							: null;
		$this->updateMatch 	= isset($_POST['updateMatch']) 	? 1 							: null;
	}
	
	function init() {
		$this->setTitle('NO TITLE SET');
		
		// TEAM SPECIFIC ACTIONS

		if($this->createTeam && $this->name) {
			$this->team->create($this->name);
		}

		if($this->updateTeam && $this->name) {
			$this->team->setName($this->post_id, $this->name);
		}

		if($this->deleteTeam) {
			$this->team->delete($this->deleteTeam);
		}

		// MATCH SPECIFIC ACTIONS

		if($this->createMatch && $this->date && $this->team1 && $this->team2 && $this->odd1 && $this->odd2) {
			$this->match->create($this->date, $this->team1, $this->team2, $this->odd1, $this->odd2);
			
			$this->date = '';
			$this->team1 = ''; 
			$this->team2 = ''; 
			$this->odd1 = '';
			$this->odd2 = '';
		}
		
		if($this->updateMatch) {
			$this->match->update($this->post_id, $this->date, $this->team1, $this->team2, $this->odd1, $this->odd2, $this->winner);

			$this->post_id = '';
			$this->date = '';
			$this->team1 = ''; 
			$this->team2 = ''; 
			$this->odd1 = '';
			$this->odd2 = '';
		}
		
		$match = isset($_GET['match']) ? intval($_GET['match']) : 0;
		$winner = isset($_GET['winner']) ? intval($_GET['winner']) : 0;
		if($match && $winner) {
			$this->match->setWinner($match, $winner);
		}

		if($this->deleteMatch) {
			$this->match->delete($this->deleteMatch);
		}
	}

	function setTitle($title) {
		$this->title = 
'				<h1 class="page-title">' . $title . '</h1>';
	}

	function colorFormat($x) {
		$color = '';
		$color = $x > 0 ? 'green' : $color;
		$color = $x < 0 ? 'red' : $color;
		return '<span class="' . $color . '">' . number_format($x, 2) . '</span>';
	}

	function html($page)
	{
		include('Body.nba.' . $page . '.php');

		$date = $this->date ? $this->date : date("Y-m-d");

		$this->html = 
'	<wrapper>
		<header>
			<div id="header-left">
				<a href="index.php">Home</a> 
				<a href="teams.nba.php">Teams</a> ';
		if($this->cm) {
			$this->html .= '<a href="matches.nba.php">Matches</a> ';
		}
		$this->html .=		 
'				<a href="odds.nba.php">Odds</a> 
				<a href="bets.nba.php">Bets</a> 
			</div>';
			if($this->cm) {
				$this->html .= 
'			<div id="header-right">
				<form action="index.php" method="POST">
					<input type="hidden" name="createMatch" value="1">
					<input type="date" name="date" value="' . $date . '"> 
					<select name="team1"><option value="0">Team1</option>' . $this->team->getSelectList($this->team1) . '</select> 
					<select name="team2"><option value="0">Team2</option>' . $this->team->getSelectList($this->team2) . '</select>
					<input type="text" name="odd1" value="' . $this->odd1 . '"> <input type="text" name="odd2" value="' . $this->odd2 . '"> 
					<input type="submit" value="Create"> CM: ' . $this->cm . ' Logout: ' . $this->logout . ' 
				</form>
				<div>
					<a href="index.php?cm=1962">Login</a> | <a href="index.php?logout=1">Logout</a>
				</div>
			</div>';
			}
			$this->html .=		 
'		</header>
		<main>';

		$showMainLeft = ($page == 'index');
		$fullWidth = $showMainLeft ? '' : ' style="width:100%"';
		$this->html .= $showMainLeft ? '
			<section id="main-left">
' . $this->match->matchGetLeftMatches->getLeftMatches() . '
			</section>' : '';
		$this->html .= '
			<section id="main-right"' . $fullWidth . '>
' . $this->title . '
' . $html . '
			</section>
		</main>
	</wrapper>
	<script>
		function showMatches(date) {
			getMatches(date);
			toggleVisibility(date);
		}

		function toggleVisibility(id) {
			const el = document.getElementById(id);
			const elDisplay = el.style.display;
			if(elDisplay == "none") {
				el.style.display = "block";
			} else {
				el.style.display = "none";
			}
		}

		async function getMatches(date) {
			let res = await fetch("api/matches.php?date=" + date);
			let html = await res.text();
			const div = document.getElementById("inner" + date);
			div.innerHTML = html;
		}

		async function getBetDetail(id) {
			let res = await fetch("api/betdetail.php?id=" + id);
			let html = await res.text();
			const div = document.getElementById("bet" + id);
			div.innerHTML = html;
		}

		function handleDelete(id) {
			const deleteID = "delete" + id;
			console.log(deleteID);
			const myDelete = document.getElementById(deleteID);
			myDelete.style.display = "none";
			const confirmID = "confirmDelete" + id;
			const confirm = document.getElementById(confirmID);
			confirm.style.display = "block";
		}

		</script>';
	}
}
?>