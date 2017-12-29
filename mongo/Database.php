<?php
require 'vendor/autoload.php';
require 'HelperFunctions.php';
require 'Singleton.php';

//MongoDB class
class Database extends Singleton
{
	private $username, $password, $databaseName, $client, $database;

	public function setCredentials($username = 'alex', $password = 'proiectsac')
	{
		$this->username = $username;
		$this->password = $password;
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
		Returns the specified player or a random one
	*/
	public function getPlayer($collection, $random = True, $playerID = null)
	{
		$playersCount = $collection->count();

		if($random)
		{
			$playerID = mt_rand(0, $playersCount);
		}

		$cursor = $collection->find([
			'_id' => $playerID
		]);

		foreach ($cursor as $randomPlayer) 
		{
		   $playerArray = bsonUnserialize($randomPlayer);
		};

		return json_encode($playerArray);
	}

	public function getStartingEleven($teamCollection, $playerCollection, $managerID)
	{
		$cursor = $teamCollection->find([
			'_id' => $managerID
		]);

		foreach ($cursor as $activeTeam) 
		{
		   $startingEleven = bsonUnserialize($activeTeam);
		};

		$startingTeam = $startingEleven["players"];

		$startingPlayers = [];

		foreach($startingTeam as $playerID) 
		{
		   $player = $this->getPlayer($playerCollection, False, $playerID);
		   $startingPlayers[] = $player;
		};

		return $startingPlayers;
	}

	public function getDatabase()
	{
		return $this->database;
	}
}
?>