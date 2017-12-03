<?php
require_once 'User.php';
require_once 'Feedback.php';
require_once 'idiorm.php';
require_once 'session.php';

if(isset($_POST['contact_button']))
{
	$name = trim($_POST['contact_name']);
	$email = trim($_POST['contact_email']);
	$message = trim($_POST['contact_feedback']);
	
	$feedback = new Feedback($name, $email, $message);
	
	User::increaseRankByEmail($email);
	
	redirect('../index.php', 'Message succesfully sent!');
}
?>