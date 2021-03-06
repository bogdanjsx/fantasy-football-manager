<?php
require_once 'session.php';

class User
{
	private $ORM = null;
	private $username, $password, $email, $managerID;
	
	/**
	 * Create a user by the parameters given in the database
	*/
	public function __construct($username, $password, $email, $managerID)
	{
		$this->ORM = ORM::for_table('users')->create();
		$this->ORM->username = $username;
		$this->ORM->password = md5($password);
		$this->ORM->email = $email;
		$this->ORM->managerID = $managerID;
		$this->ORM->save();
	}
	
	//Used for login
	public static function findUser($username, $password)
	{
		$user = ORM::for_table('users')->where('username', $username)->find_one();
		
		if($user->password == md5($password))
		{
			return $user;
		}

		//If credentials are not correct
		return null;
	}
	
	//Check is user exists by the email given
	public static function userExistsByEmail($email)
	{
		$ORMResult = ORM::for_table('users')->where('email', $email)->count();
 
		return $ORMResult == 1;
	}
	
	//Check is user exists by the username given
	public static function userExistsByUsername($username)
	{
		$ORMResult = ORM::for_table('users')->where('username', $username)->count();
 
		return $ORMResult == 1;
	}

	//Login the user
	public static function login($ORM)
	{
		$_SESSION['logID'] = $ORM->id;
		$_SESSION['username'] = $ORM->username;
		$_SESSION['email'] = $ORM->email;
		$_SESSION['managerID'] = $ORM->managerID;
	}
	
	//Logout the user 
	public function logout()
	{
		$_SESSION = array();
		unset($_SESSION);
	}
	
	public function __get($arg)
	{
		if($arg != 'password' && isset($this->orm->$arg))
		{
			return $this->orm->$arg;
		}

		return null;
	}
}
?>