<?php
require_once 'session.php';

class User
{
	private $ORM = null;
	private $username, $password, $email;
	
	/*
		Rank 1 is admin;
		Rank 2 is feedbacking user;
		Rank 3 is common user;
	*/
	private $rank;
	
	/**
	 * Create a user by the parameters given in the database
	*/
	public function __construct($username, $password, $email)
	{
		$this->ORM = ORM::for_table('users')->create();
		$this->ORM->username = $username;
		$this->ORM->password = md5($password);
		$this->ORM->email = $email;
		$this->ORM->rank = 3;
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
	
	//Increase rank of feedbacking users to 2
	public static function increaseRankByEmail($email)
	{
		$user = ORM::for_table('users')->where('email', $email)->find_one();
		
		if($user != null)
		{
			$user->rank = 2;
			$user->save();
		}
	}
	
	//Increase rank of game rating users to 2
	public static function increaseRankByUsername($username)
	{
		$user = ORM::for_table('users')->where('username', $username)->find_one();
		
		if($user != null)
		{
			$user->rank = 2;
			$user->save();
		}
	}
	
	//Returns all users that have rank 2
	public static function getAllRank2Users()
	{
		$users = ORM::for_table('users')->where('rank',2)->find_many();
		
		return $users;
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