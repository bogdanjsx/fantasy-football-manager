<?php
require 'vendor/autoload.php';
require 'HelperFunctions.php';

//MongoDB class
class Database
{
	private $username, $password, $databaseName, $client, $database;

	public function __construct($username = 'alex', $password = 'proiectsac')
	{
		$this->username = "alex";
		$this->password = "proiectsac";
		$this->databaseName = "fantasy-football-manager";
		$this->client = new MongoDB\Client("mongodb://ds249025.mlab.com:49025/fantasy-football-manager", 
						array(
							"username" => $this->username, 
							"password" => $this->password)
						);

		try
		{
			$this->database = $this->client->selectDatabase($this->databaseName);
		}
		catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)
		{
		    echo $e->getMessage();
		}
	}

	public function connectToTable($tableName = 'player_classes')
	{
		try
		{
			$playerCollection = $this->database->selectCollection($tableName);
		}
		catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)
		{
		    echo $e->getMessage();
		}

		return $playerCollection;
	}

	/**
		Returns any player, including silver and bronze ones
	*/
	public function getRandomPlayer($playerCollection)
	{
		$playersCount = $playerCollection->count();

		$randomID = mt_rand(0, $playersCount);

		$cursor = $playerCollection->find([
			'_id' => $randomID
		]);

		foreach ($cursor as $randomPlayer) 
		{
		   $playerArray = bsonUnserialize($randomPlayer);
		};

		return json_encode($playerArray);
	}

	

	public function getDatabase()
	{
		return $this->database;
	}
}
?>