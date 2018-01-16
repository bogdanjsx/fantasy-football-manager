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
		Returns the player with the specified ID or a random one
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
	
	//Doesn't return the players belonging to the specified manager
	public function getTransferMarketPlayers($myManagerID, $transferMarketCollection, $playersCollection)
	{
		$cursorTransferMarket = $transferMarketCollection->find([
			'manager_id' => ['$ne' => $myManagerID]
		]);

		$marketDetails = [];

		foreach ($cursorTransferMarket as $marketEntry) 
		{
		   $objMarketEntry = bsonUnserialize($marketEntry);
		   $player = $this->getPlayer($playersCollection, False, $objMarketEntry["player_id"]);

		   $marketDetails[] = [
		   		"player_id" => $player,
		   		"price" => (int)$objMarketEntry["price"],
		   		"ownerId" => (int)$objMarketEntry["manager_id"]
		   	];
		}

		return $marketDetails;
	}

	//Returns players currently selling in the transfer market for a manager
	public function getSellingPlayers($myManagerID, $playersCollection, $managerCollection)
	{
		$cursorManager = $managerCollection->find([
			'_id' => $myManagerID
		]);

		foreach ($cursorManager as $managerInformation) 
		{
		   $managerInfo = bsonUnserialize($managerInformation);
		};

		$sellingPlayers = $managerInfo["market_players"];

		$playersArray = [];

		foreach($sellingPlayers as $playerID) 
		{
		   $player = $this->getPlayer($playersCollection, False, $playerID);
		   $playersArray[] = $player;
		};

		return $playersArray;
	}

	public function sellPlayer($sellingPlayerID, $coins, $managerID, $managerCollection, $playerCollection, $transferMarketCollection, $activeTeamsCollection)
	{
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

		if(!in_array($sellingPlayerID, $benchedIDs))
		{
			throw new Exception("Cannot sell player with ID " . $sellingPlayerID . " because he is not in the benched players list " . json_encode($benchedIDs));
		}

		$cursor = $managerCollection->find([
			'_id' => $managerID
		]);

		foreach($cursor as $managerDetails) 
		{
		   $managerInfo = bsonUnserialize($managerDetails);
		};

		$allPlayers = (array)($managerInfo["players"]);
		$transferredPlayers = $managerInfo["market_players"];

		if(($key = array_search($sellingPlayerID, $allPlayers)) !== false)
		{
			unset($allPlayers[$key]);
			$allPlayers = array_values($allPlayers);
			$transferredPlayers[] = (int)$sellingPlayerID;
		}
		else
		{
			throw new Exception("Player sell: Didn't find benched player with ID " . $sellingPlayerID);
		}
		
		$managerCollection->updateOne(
			['_id' => $managerID],
			['$set' => [
				'players' => $allPlayers,
				'market_players' => $transferredPlayers
				]
			]
		);

		$transferMarketCollection->insertOne([
			'player_id' => (int)$sellingPlayerID,
			'price' => (int)$coins,
			'manager_id' => (int)$managerID
		]
		);
		
	}

	//If managerOwnerID = myManagerID, then the player in cause is sent back to the team
	public function buyPlayer($newPlayerID, $managerOwnerID, $myManagerID, $managerCollection, $transferMarketCollection, $playerCollection, $activeTeamsCollection)
	{
		//Check if player is already in my team
		$allMyPlayers = $this->getAllPlayers($myManagerID, $managerCollection, $playerCollection, $activeTeamsCollection, True);
		$myIDs = [];

		foreach($allMyPlayers as $playerArray)
		{
			$objPlayer = json_decode($playerArray);
			$vars = get_object_vars($objPlayer);
			$playerID = $vars["_id"];
			$myIDs[] = $playerID;
		}

		if(in_array($newPlayerID, $myIDs))
		{
			throw new Exception("Cannot buy player with ID " . $newPlayerID . " because he is already in my team " . json_encode($myIDs));
		}

		//Check if I have enough coins to buy the player
		$cursorTransferMarket = $transferMarketCollection->find([
			'player_id' => $newPlayerID
		]);

		foreach($cursorTransferMarket as $objTransactionDetails) 
		{
		   $transactionDetails = bsonUnserialize($objTransactionDetails);
		};

		$playerValue = $transactionDetails['price'];

		$cursorManager = $managerCollection->find([
			'_id' => $myManagerID
		]);

		foreach($cursorManager as $objManagerDetails) 
		{
		   $managerDetails = bsonUnserialize($objManagerDetails);
		};


		$myCoins = $managerDetails["coins"];

		$myAllPlayers = (array)($managerDetails["players"]);
		$myTransferredPlayers = (array)$managerDetails["market_players"];

		//Cancel a previous transaction
		if($managerOwnerID == $myManagerID)
		{
			$myAllPlayers[] = (int)$newPlayerID;

			$key = array_search($newPlayerID, $myTransferredPlayers);
			unset($myTransferredPlayers[$key]);
			$myTransferredPlayers = array_values($myTransferredPlayers);

			$managerCollection->updateOne(
				['_id' => $myManagerID],
				['$set' => [
					'players' => $myAllPlayers,
					'market_players' => $myTransferredPlayers
					]
				]
			);

			$transferMarketCollection->deleteOne([
				'player_id' => (int)$newPlayerID,
				'manager_id' => (int)$managerOwnerID
			]
			);
			
		}
		//Buy a new player
		else
		{
			if($myCoins < $playerValue)
			{
				throw new Exception("Cannot buy player with ID " . $newPlayerID . " because I don't have enough coins.");
			}

			//Update my details
			$myAllPlayers[] = (int)$newPlayerID;
			$myCoins -= $playerValue;

			$managerCollection->updateOne(
				['_id' => $myManagerID],
				['$set' => [
					'players' => $myAllPlayers,
					'coins' => (int)$myCoins
					]
				]
			);

			//Update old owner's details
			$cursorOtherManager = $managerCollection->find([
				'_id' => $managerOwnerID
			]);

			foreach($cursorOtherManager as $objManagerDetails) 
			{
			   $otherManagerDetails = bsonUnserialize($objManagerDetails);
			};

			$transferredPlayers = (array)$otherManagerDetails["market_players"];

			$key = array_search($newPlayerID, $transferredPlayers);
			unset($transferredPlayers[$key]);
			$transferredPlayers = array_values($transferredPlayers);

			$managerCoins = $otherManagerDetails["coins"];
			$managerCoins += $playerValue;

			$managerCollection->updateOne(
				['_id' => $managerOwnerID],
				['$set' => [
					'coins' => (int)$managerCoins,
					'market_players' => $transferredPlayers
					]
				]
			);
			
			//Delete transaction from transfer market
			$transferMarketCollection->deleteOne([
				'player_id' => (int)$newPlayerID,
				'manager_id' => (int)$managerOwnerID
			]
			);
			
		}
	}

	//Used only once for collection creation
	public function createClubsCollection($clubsCollection, $playersCollection)
	{
		$DBClubs = $playersCollection->distinct('club');

		$clubArray = [];

		foreach($DBClubs as $club)
		{
			$cursorDB = $playersCollection->find([
				'club' => $club
			]);

			foreach($cursorDB as $playerDetails)
			{
				$playerArray = bsonUnserialize($playerDetails);
				$clubLogo = $playerArray["club_logo"];
			}

			$clubArray = [
				"_id" => $club,
				"logo" => $clubLogo
			];

			$clubsCollection->insertOne($clubArray);
		}
	}

	//Used only once for collection creation
	public function createCountriesCollection($countriesCollection, $playersCollection)
	{
		$DBNationalities = $playersCollection->distinct('nationality');

		$nationalityArray = [];

		foreach($DBNationalities as $nationality)
		{
			$cursorDB = $playersCollection->find([
				'nationality' => $nationality
			]);

			foreach($cursorDB as $playerDetails)
			{
				$playerArray = bsonUnserialize($playerDetails);
				$nationalityLogo = $playerArray["flag"];
			}

			$nationalityArray = [
				"_id" => $nationality,
				"logo" => $nationalityLogo
			];

			$countriesCollection->insertOne($nationalityArray);
		}
	}

	public function getMyStats($managerID, $managersCollection, $clubsCollection)
	{
		$cursorManager = $managersCollection->find([
			'_id' => $managerID
		]);

		foreach($cursorManager as $objManagerDetails) 
		{
		   $managerDetails = bsonUnserialize($objManagerDetails);
		};

		$teamName = $managerDetails["team_name"];
		$record = $managerDetails["record"];
		$goalDifference = $managerDetails["goal_difference"];
		$coins = $managerDetails["coins"];

		$cursorCountries = $clubsCollection->find([
			'_id' => $managerDetails["favourite_team"]
		]);

		foreach($cursorCountries as $objClubDetails)
		{
			$clubDetails = bsonUnserialize($objClubDetails);
		}

		$teamLogo = $clubDetails["logo"];

		$myStatsArray = [
			"team_name" => $teamName,
			"favourite_team_logo" => $teamLogo,
			"record" => $record,
			"goal_difference" => $goalDifference,
			"coins" => (int)$coins
		];

		return $myStatsArray;
	}

	public function getClubs($clubsCollection)
	{
		$clubsCursor = $clubsCollection->find();

		$clubsInfo = [];
		foreach ($clubsCursor as $clubInformation) 
		{
		   $clubInfo = bsonUnserialize($clubInformation);
		   $clubsInfo[] = $clubInfo;
		};

		return $clubsInfo;
	}
	//Returns a GK, defender, midfielder and attacker
	public function getFavouredPlayers($favouriteTeam, $playersCollection)
	{
		$playerCursor = $playersCollection->find([
			'club' => $favouriteTeam
		]);

		$clubPlayers = [];

		foreach ($playerCursor as $player) 
		{
		   $playerArray = bsonUnserialize($player);
		   $clubPlayers[] = $playerArray;
		};

		$goalkeeper = null;
		$defender = null;
		$midfielder = null;
		$attacker = null;

		shuffle($clubPlayers);
		foreach($clubPlayers as $clubPlayer)
		{
			$positions = (array)$clubPlayer['positions'];

			if(is_null($goalkeeper) && count(array_intersect(['GK'], $positions)) > 0)
			{
				$goalkeeper = $clubPlayer;
			}
			else if(is_null($defender) && count(array_intersect(['CB', 'LB', 'RB', 'LWB', 'RWB'], $positions)) > 0)
			{
				$defender = $clubPlayer;
			}
			else if(is_null($midfielder) && count(array_intersect(['CDM', 'CM', 'CAM', 'RM', 'LM', 'LW', 'RW'], $positions)) > 0)
			{
				$midfielder = $clubPlayer;
			}
			else if(is_null($attacker) && count(array_intersect(['ST', 'CF'], $positions)) > 0)
			{
				$attacker = $clubPlayer;
			}
		}

		$teamPicks = [
			"goalkeeper" => $goalkeeper,
			"defender" => $defender,
			"midfielder" => $midfielder,
			"attacker" => $attacker,
		];
		
		return $teamPicks;
	}

	//Gets 15 random players for a manager
	public function createNewManager($managerID, $favouriteTeam, $teamName, $playersCollection, $managersCollection, $activeTeamsCollection)
	{
		$teamPicks = $this->getFavouredPlayers($favouriteTeam, $playersCollection);
		$teamPicksIDs = [];

		foreach($teamPicks as $teamPick)
		{
			$teamPicksIDs[] = $teamPick['_id'];
		}

		while(count($teamPicksIDs) < 15)
		{
			$randomPlayer = (array)json_decode($this->getPlayer($playersCollection));

			if(!in_array($randomPlayer['_id'], $teamPicksIDs))
			{
				$teamPicksIDs[] = $randomPlayer['_id'];
			}
		}

		$this->createDBObjects($teamPicksIDs, $managerID, $favouriteTeam, $teamName, $playersCollection, $managersCollection, $activeTeamsCollection);
	}

	//Create DB object
	public function createDBObjects($teamPicksIDs, $managerID, $favouriteTeam, $teamName, $playersCollection, $managersCollection, $activeTeamsCollection)
	{	
		$managersCollection->insertOne([
			'_id' => (int)$managerID,
			'players' => $teamPicksIDs,
			'team_name' => $teamName,
			'favourite_team' => $favouriteTeam,
			'record' => [
				'wins' => (int)0,
				'draws' => (int)0,
				'losses' => (int)0
			],
			'goal_difference' => [
				'goals_for' => (int)0,
				'goals_against' => (int)0
			],
			'coins' => (int)0,
			'market_players' => []
		]
		);

   		$activeTeamsCollection->insertOne([
			'_id' => (int)$managerID,
			'players' => [
				'LST' => [
					'id' => $teamPicksIDs[3],
					'chemistry' => (int)1
				],
				'RST' => [
					'id' => $teamPicksIDs[4],
					'chemistry' => (int)1
				],
				'LM' => [
					'id' => $teamPicksIDs[5],
					'chemistry' => (int)1
				],
				'LCM' => [
					'id' => $teamPicksIDs[2],
					'chemistry' => (int)1
				],
				'RCM' => [
					'id' => $teamPicksIDs[6],
					'chemistry' => (int)1
				],
				'RM' => [
					'id' => $teamPicksIDs[7],
					'chemistry' => (int)1
				],
				'LB' => [
					'id' => $teamPicksIDs[8],
					'chemistry' => (int)1
				],
				'LCB' => [
					'id' => $teamPicksIDs[1],
					'chemistry' => (int)1
				],
				'RCB' => [
					'id' => $teamPicksIDs[9],
					'chemistry' => (int)1
				],
				'RB' => [
					'id' => $teamPicksIDs[10],
					'chemistry' => (int)1
				],
				'GK' => [
					'id' => $teamPicksIDs[0],
					'chemistry' => (int)1
				],
			]
		]
		);
	}
}
?>