<?php
require_once 'session.php';

class GameRating
{
	private $ORM = null;
	private $gameID, $rating, $username;
	
	/*
		Create a new entry in the database
	*/
	public function __construct($gameID, $rating, $username)
	{
		$this->ORM = ORM::for_table('game_rating')->create();
		$this->ORM->game_id = $gameID;
		$this->ORM->rating = $rating;
		$this->ORM->username = $username;
		$this->ORM->save();
	}
}
?>