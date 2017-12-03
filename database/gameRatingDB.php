<?php
require_once 'User.php';
require_once 'GameRating.php';
require_once 'idiorm.php';
require_once 'session.php';

if(isset($_POST['game_submit_button']))
{
	$game_id = trim($_POST['game_id']);
	$rating = trim($_POST['rating']);
	$username = trim($_POST['username']);
	
	$gameRating = new GameRating($game_id, $rating, $username);
	
	User::increaseRankByUsername($username);
	
	redirect('../index.php', 'Rating succesfully sent!');
}
?>