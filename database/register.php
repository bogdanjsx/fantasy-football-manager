<?php
require_once 'User.php';
require_once 'idiorm.php';
require_once 'session.php';
require_once '../mongo/Database.php';

if(isset($_POST['register_button']))
{
	$username = trim($_POST['user']);
	$email = trim($_POST['email']);
	$password = trim($_POST['pw']);
	$passwordRepeat = trim($_POST['pw-repeat']);
	$teamName = trim($_POST['team_name']);
	$favTeam = trim($_POST['fav_team']);
	
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
	
	//Get a managerID
	$mongoDB = Database::instance();
	$mongoDB->setCredentials();
	$playersCollection = $mongoDB->connectToTable('player_classes');
	$activeTeamsCollection = $mongoDB->connectToTable('active_teams');
	$managersCollection = $mongoDB->connectToTable('managers');

	$newManagerID = $mongoDB->getNewManagerID($managersCollection);
	$mongoDB->createNewManager($newManagerID, $favTeam, $teamName, $playersCollection, $managersCollection, $activeTeamsCollection);

	$user = new User($username, $password, $email, $newManagerID);

	//Redirect the user
	redirect('../index.php', "Account succesfully created!");
}
?>