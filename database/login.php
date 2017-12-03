<?php
require_once 'User.php';
require_once 'idiorm.php';
require_once 'session.php';

if(isset($_POST['login_button']))
{
	$username = trim($_POST['user']);
	$password = trim($_POST['pw']);
	
	$user = User::findUser($username, $password);
	if($user == null)
	{
		redirect("../login.php", "Username and password combination do not exist!");
	}
	
	ob_start();
	session_start();
	
	session_name('cormorant');
	//Keep login for a day
	session_set_cookie_params(60 * 60 * 24);
	
	User::login($user);

	redirect("../index.php", "Welcome " . $user->username);
}
?>