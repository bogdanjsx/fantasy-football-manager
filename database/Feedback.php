<?php
require_once 'session.php';

class Feedback
{
	private $ORM = null;
	private $text, $name, $email;
	
	/*
		Create a new entry in the database
	*/
	public function __construct($name, $email, $text)
	{
		$this->ORM = ORM::for_table('feedback')->create();
		$this->ORM->name = $name;
		$this->ORM->text = $text;
		$this->ORM->email = $email;
		$this->ORM->save();
	}
	
	public static function getMessages()
	{
		$messages = ORM::for_table('feedback')->find_many();
	
		return $messages;
	}
}
?>