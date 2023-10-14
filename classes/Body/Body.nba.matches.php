<?
	if(!$this->cm) die();
	
	include('Match/MatchGetRightMatches.php');
	$this->matchGetRightMatches = new MatchGetRightMatches($this);

	$get_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$id = $get_id + $post_id;
	
	if($id) {
		$this->setTitle('EDIT MATCH ' . $id);
		$html = $this->match->edit($id);
	} else {
		$this->setTitle('MATCHES');
		$html = $this->matchGetRightMatches->getRightMatches();
	}
?>