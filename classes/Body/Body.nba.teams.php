<?
	$this->setTitle('TEAMS');

	$id = $this->get_id + $this->post_id;

	$html = $id ? $this->team->show($id) : $this->team->listTeams();
?>