<?php
include('Match/MatchGetLeftMatches.php');

class MyMatch {
    public $body;

    function __construct($body) {
        $this->body = $body;
        $this->matchGetLeftMatches = new MatchGetLeftMatches($body);
    }

    function create($date, $team1, $team2, $odd1, $odd2) {
        $sql = 'insert into matches set date="' . $date . '", team1=' . $team1 . ', team2=' . $team2 . ', odd1=' . $odd1 . ', odd2=' . $odd2 . ', todd1=' . floor(10 * $odd1) . ', todd2=' . floor(10 * $odd2);
        $this->body->db->query($sql);
    }

    function get($id, $field) {

        // $bt = debug_backtrace();
        // echo 'BT: ' . $bt[1]['function'] . '<br>';
        // var_dump ($bt[1]['function']); // die();
        // echo '<br>';

        $sql = 'select ' . $field . ' from matches where id=' . $id; // echo "Match::get $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $data = $row[$field];
        $data = $field == 'odd1' || $field == 'odd2' ? number_format(round(100 * $data) / 100, 2) : $data;
        // echo "MyMatch::get $field $id $data<br>";
        return $data;
    }

	function edit($id) {
		$date = $this->get($id, 'date');
        $team1 = $this->get($id, 'team1');
        $team2 = $this->get($id, 'team2');
        $odd1 = $this->get($id, 'odd1');
        $odd2 = $this->get($id, 'odd2');
        $winner = $this->get($id, 'winner');;

		$html = '
		<section id="edit-match">
			<form action="matches.nba.php" method="POST">
                <input type="hidden" name="updateMatch" value="1">
                <input type="hidden" name="id" value="' . $id . '">
                <input type="date" name="date" value="' . $date . '">
				<select name="team1">
					<option value="0">Team 1</option>'
					. $this->body->team->getSelectList($team1) .
'				</select>
				<select name="team2">
					<option value="0">Team 2</option>'
					. $this->body->team->getSelectList($team2) .
'				</select>
				<input type="text" name="odd1" value="' . $odd1 . '">
				<input type="text" name="odd2" value="' . $odd2 . '">
				<input type="text" name="winner" value="' . $winner . '">
				<input type="submit" value="Submit">
			</form>
		<section>';

		return $html;
	}	    

    function update($id, $date, $team1, $team2, $odd1, $odd2, $winner) {
        $sql = 'update matches set date="' . $date . '", team1=' . $team1 . ', team2=' . $team2 . ', odd1=' . $odd1 . ', odd2=' . $odd2 . ', todd1=' . floor(10 * $odd1) . ', todd2=' . floor(10 * $odd2) . ', winner=' . $winner . '  where id=' . $id;
        $this->body->db->query($sql);	

        if($winner) $this->setWinner($id, $winner);
    }

    function setWinner($id, $winner) {
        $sql = 'update matches set winner=' . $winner . ', final=1 where id=' . $id;
        $this->body->db->query($sql);
        
        $date = $this->get($id, 'date');
        $team1 = $this->get($id, 'team1');
        $team2 = $this->get($id, 'team2');
        $odd1 = $this->get($id, 'odd1');
        $odd2 = $this->get($id, 'odd2');
        $winner = $this->get($id, 'winner');;

        $this->body->team->updateGains($team1);
        $this->body->team->updateGains($team2);

        $this->body->team->updateSuccess($team1);
        $this->body->team->updateSuccess($team2);
        
        $this->body->team->updateRecord($team1, $date);
        $this->body->team->updateRecord($team2, $date);

        $todd1 = floor(10 * $odd1);
        $todd2 = floor(10 * $odd2);

        $this->body->oddGains->update($todd1);
        $this->body->oddGains->update($todd2);
    
        $this->body->oddSuccess->update($todd1);
        $this->body->oddSuccess->update($todd2);

        $this->body->oddRecord->update($todd1, $date, $winner == 1);
        $this->body->oddRecord->update($todd2, $date, $winner == 2);
    }

    function delete($id) {
        $sql = 'delete from matches where id=' . $id;
        $this->body->db->query($sql);
    }

    function build() {
        // update match bets => bet->build();
    }
}
?>