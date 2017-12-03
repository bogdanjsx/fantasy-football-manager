<?php
require_once 'User.php';
require_once 'idiorm.php';
require_once 'session.php';

if(isset($_POST['register_button']))
{
	$username = trim($_POST['user']);
	$email = trim($_POST['email']);
	$password = trim($_POST['pw']);
	$passwordRepeat = trim($_POST['pw-repeat']);
	
	//Reload page if passwords don't match
	if($password != $passwordRepeat)
	{
		redirect('../index.php', "Passwords do not match!");
	}
	//Reload page if e-mail is already in use
	if(User::userExistsByEmail($email))
	{
		redirect('../index.php', "Email is already in use!");
	}
	//Reload page if user is already in use
	if(User::userExistsByUsername($username))
	{
		redirect('../index.php', "Username is already in use!");
	}
	
	$user = new User($username, $password, $email);

	//Redirect the user
	redirect('../index.php', "Account succesfully created!");
}
?>