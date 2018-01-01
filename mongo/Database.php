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
		Returns the player on the specified position or a random one
	*/
	public function getPlayer($playerCollection, $random = True, $playerID = null)
	{
		$playersCount = $playerCollection->count();

		if($random)
		{
			$playerID = mt_rand(0, $playersCount);
		}

		$cursor = $playerCollection->find([
			'_id' => $playerID
		]);

		foreach ($cursor as $player) 
		{
		   $playerArray = bsonUnserialize($player);
		};

		return json_encode($playerArray);
	}

	//Gets chemistry of a starting 11 player
	public function getChemistry($teamMates, $playerCollection, $playerID, $playerPosition)
	{
		//Check if position is valid
		$validPositions = ["RST", "LST", "LM", "LCM", "RCM", "RM", "LB", "LCB", "RCB", "RB", "GK"];

		if(!in_array($playerPosition, $validPositions))
		{
			throw new Exception("Chemistry calculation: Invalid player position " . $playerPosition . " not found in " . json_encode($validPositions));
		}

		$cursor = $playerCollection->find([
			'_id' => $playerID
		]);

		foreach ($cursor as $player) 
		{
		   $playerArray = bsonUnserialize($player);
		   $club = $playerArray["club"];
		   $nationality = $playerArray["nationality"];
		   $positions = bsonUnserialize($playerArray["positions"]);

		   foreach($positions as $key => $position)
		   {
		   		switch($position)
		   		{
		   			case "LW":
		   				$positions[$key] = "LM";
		   				break;

		   			case "RW":
		   				$positions[$key] = "RM";
		   				break;

		   			case "CAM":
		   				$positions[$key] = "CM";
		   				break;
		   				
		   			case "CDM":
		   				$positions[$key] = "CM";
		   				break;

		   			case "RWB":
		   				$positions[$key] = "RB";
		   				break;	
		   				
		   			case "LWB":
		   				$positions[$key] = "LB";
		   				break;					
		   		}
		   }
		};

		$chemistryRating = 1;

		//Get position chemistry
		if(in_array($playerPosition, ["LST", "RST"]))
		{
			$realPosition = "ST";
		}
		else if(in_array($playerPosition, ["LCM", "RCM"]))
		{
			$realPosition = "CM";
		}
		else if(in_array($playerPosition, ["LCB", "RCB"]))
		{
			$realPosition = "CB";
		}
		else
		{
			$realPosition = $playerPosition;
		}

		if($realPosition == $positions[0])
		{
			$chemistryRating += 3;
		}
		else if(in_array($realPosition, $positions))
		{
			$chemistryRating += 2;
		}

		$linkedTeammates = [
			"LST" => ["RST", "LM", "LCM"],
			"RST" => ["LST", "RM", "RCM"],
			"LM" => ["LST", "LB", "LCM"],
			"LCM" => ["LM", "LST", "LCB", "RCM"],
			"RCM" => ["RST", "LCM", "RM", "RCB"],
			"RM" => ["RST", "RCM", "RB"],
			"LB" => ["LM", "LCB"],
			"LCB" => ["LB", "LCM", "RCB", "GK"],
			"RCB" => ["LCB", "RCM", "RB", "GK"],
			"RB" => ["RM", "RCB"],
			"GK" => ["LCB", "RCB"]
		];

		$linkedPositions = $linkedTeammates[$playerPosition];

		foreach($linkedPositions as $linkedPosition)
		{
			if($teamMates[$linkedPosition]["club"] == $club)
			{
				$chemistryRating += 4;
			}
			else if($teamMates[$linkedPosition]["nationality"] == $nationality)
			{
				$chemistryRating += 2;
			}
		}
	
		if($chemistryRating > 10)
		{
			$chemistryRating = 10;
		}

		return $chemistryRating;
	}

	public function getStartingEleven($activeTeamsCollection, $playerCollection, $managerID)
	{
		$cursor = $activeTeamsCollection->find([
			'_id' => $managerID
		]);

		foreach ($cursor as $activeTeam) 
		{
		   $startingEleven = bsonUnserialize($activeTeam);
		};

		$startingTeam = $startingEleven["players"];

		$startingPlayers = [];

		foreach($startingTeam as $position => $playerArray) 
		{
		    $player = $this->getPlayer($playerCollection, False, $playerArray["id"]);

		   	$startingPlayers[$position] = [
		   		"player" => $player,
		   		"chemistry" => 1
		   	];
		};

		$teamMates = [];

		foreach($startingPlayers as $position => $teammate)
		{
			$decodedPlayer = json_decode($teammate["player"]);
			$vars = get_object_vars($decodedPlayer);

			$teamMates[$position] = [
				"nationality" => $vars["nationality"],
				"club" => $vars["club"]
			];
		}

		foreach($startingTeam as $position => $playerArray) 
		{
		    $startingPlayers[$position]["chemistry"] = $this->getChemistry($teamMates, $playerCollection, $playerArray["id"], $position);
		};

		//Update DB
		$outputArray = [];

		foreach($startingPlayers as $position => $playerArray)
		{
			$objPlayer = json_decode($playerArray["player"]);
			$vars = get_object_vars($objPlayer);
			$playerID = $vars["_id"];
			$playerChemistry = $startingPlayers[$position]["chemistry"];
			
			$outputArray[$position] = [
				"id" => (int)$playerID,
				"chemistry" => $playerChemistry
			];
		}

		$activeTeamsCollection->updateOne(
			['_id' => $managerID],
			['$set' => ['players' => $outputArray]
			]
		);

		return $startingPlayers;
	}

	//Returns all available players, including benched ones
	public function getAvailablePlayers($managerCollection, $playerCollection, $managerID)
	{
		$cursor = $managerCollection->find([
			'_id' => $managerID
		]);

		foreach ($cursor as $availablePlayers) 
		{
		   $allPlayers = bsonUnserialize($availablePlayers);
		};

		$availablePlayers = $allPlayers["players"];

		$playersArray = [];

		foreach($availablePlayers as $playerID) 
		{
		   $player = $this->getPlayer($playerCollection, False, $playerID);
		   $playersArray[] = $player;
		};

		return $playersArray;
	}

	/**
		Returns an array of all manager's team with team name, starting 11, overall team rating and record
		except for the manager $managerID who is considered the manager that is currently playing
	*/
	public function getAllManagers($managerCollection, $playerCollection, $activeTeamsCollection, $managerID)
	{
		$cursorManagers = $managerCollection->find();

		$managers = [];

		foreach ($cursorManagers as $manager) 
		{
		   $objManager = bsonUnserialize($manager);

		   if($objManager["_id"] == $managerID)
		   {
		   		continue;
		   }

		   $startingEleven = $this->getStartingEleven($activeTeamsCollection, $playerCollection, $objManager['_id']);
		  
		   $overall = 0;
		   $teamChemistry = 0;

		   foreach($startingEleven as $playerArray)
		   {
		   		$teamChemistry += $playerArray["chemistry"];
		   		$decodedPlayer = json_decode($playerArray["player"]);
		   		$vars = get_object_vars($decodedPlayer);

		   		$overall += $vars["overall"];
		   }

		   if($teamChemistry > 100)
		   {
		   		$teamChemistry = 100;
		   }

		   $overall /= 11;

		   $record = $objManager["record"];
		   $goalDifference = $objManager["goal_difference"];
		   $coins = $objManager["coins"];

		   $managers[$objManager['_id']] = array(
		   								"manager_id" => $objManager['_id'],
		   								"team_name" => $objManager['team_name'],
		   								"starting_eleven" => $startingEleven,
		   								"overall" => $overall,
		   								"chemistry" => $teamChemistry,
		   								"record" => $record,
		   								"goal_difference" => $goalDifference,
		   								"coins" => $coins
		   							);
		};

		return $managers;
	}

	//Same function as above, but in reverse
	public function getMyTeamInfo($managerCollection, $playerCollection, $activeTeamsCollection, $managerID)
	{
		$cursorManagers = $managerCollection->find();

		$managers = [];

		foreach ($cursorManagers as $manager) 
		{
		   $objManager = bsonUnserialize($manager);

		   if($objManager["_id"] != $managerID)
		   {
		   		continue;
		   }

		   $startingEleven = $this->getStartingEleven($activeTeamsCollection, $playerCollection, $objManager['_id']);
		  
		   $overall = 0;
		   $teamChemistry = 0;

		   foreach($startingEleven as $playerArray)
		   {
		   		$teamChemistry += $playerArray["chemistry"];
		   		$decodedPlayer = json_decode($playerArray["player"]);
		   		$vars = get_object_vars($decodedPlayer);

		   		$overall += $vars["overall"];
		   }

		   if($teamChemistry > 100)
		   {
		   		$teamChemistry = 100;
		   }

		   $overall /= 11;	

		   $record = $objManager["record"];
		   $goalDifference = $objManager["goal_difference"];
		   $coins = $objManager["coins"];

		   $managers[$objManager['_id']] = array(
		   								"manager_id" => $objManager['_id'],
		   								"team_name" => $objManager['team_name'],
		   								"starting_eleven" => $startingEleven,
		   								"overall" => $overall,
		   								"chemistry" => $teamChemistry,
		   								"record" => $record,
		   								"goal_difference" => $goalDifference,
		   								"coins" => $coins
		   							);
		};

		return $managers;
	}

	/*
		If $includeStartingEleven = True it returns all players of the specified manager
	*/
	public function getAllPlayers($managerID, $managerCollection, $playerCollection, $activeTeamsCollection, $includeStartingEleven = True)
	{
		$allAvailablePlayers = $this->getAvailablePlayers($managerCollection, $playerCollection, $managerID);
		$startingEleven = $this->getStartingEleven($activeTeamsCollection, $playerCollection, $managerID);

		$activePlayers = [];
		foreach($startingEleven as $playerArray)
		{
			$activePlayers[] = $playerArray["player"];
		}

		if($includeStartingEleven)
		{
			return $allAvailablePlayers;
		}
		else
		{
			return getBenchedPlayers($allAvailablePlayers, $activePlayers);
		}
	}

	public function replacePlayer($managerID, $playerPosition, $substitutePlayerID, $managerCollection, $playerCollection, $activeTeamsCollection)
	{
		//Check if position is valid
		$validPositions = ["RST", "LST", "LM", "LCM", "RCM", "RM", "LB", "LCB", "RCB", "RB", "GK"];

		if(!in_array($playerPosition, $validPositions))
		{
			throw new Exception("Player replace: Invalid player position " . $playerPosition . " not found in " . json_encode($validPositions));
		}

		//Check if player is benched
		$benchedPlayers = $this->getAllPlayers($managerID, $managerCollection, $playerCollection, $activeTeamsCollection, False);
		$benchedIDs = [];
		foreach($benchedPlayers as $playerArray)
		{
			$objPlayer = json_decode($playerArray);
			$vars = get_object_vars($objPlayer);
			$playerID = $vars["_id"];
			$benchedIDs[] = $playerID;
		}

		if(!in_array($substitutePlayerID, $benchedIDs))
		{
			throw new Exception("Invalid substitute player ID " . $substitutePlayerID . " not found in benched players list " . json_encode($benchedIDs));
		}

		$startingEleven = $this->getStartingEleven($activeTeamsCollection, $playerCollection, $managerID);		

		$teamMates = [];

		foreach($startingEleven as $position => $teammate)
		{
			$decodedPlayer = json_decode($teammate["player"]);
			$vars = get_object_vars($decodedPlayer);

			$teamMates[$position] = [
				"nationality" => $vars["nationality"],
				"club" => $vars["club"]
			];
		}

		$outputArray = [];

		foreach($startingEleven as $position => $playerArray)
		{
			if($position == $playerPosition)
			{
				$playerID = $substitutePlayerID;
			}
			else
			{
				$objPlayer = json_decode($playerArray["player"]);
				$vars = get_object_vars($objPlayer);
				$playerID = $vars["_id"];
			}
			
			$outputArray[$position] = [
				"id" => (int)$playerID,
				"chemistry" => $this->getChemistry($teamMates, $playerCollection, (int)$playerID, $position)
			];
		}

		$activeTeamsCollection->updateOne(
			['_id' => $managerID],
			['$set' => ['players' => $outputArray]
			]
		);
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
}
?>