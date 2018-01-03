<?php
require 'mongo/Database.php';
require 'mongo/Match.php';

$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

$functionName = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));

$myManagerID = 41; //For the moment, in the future will be $_SESSION['managerID']
$mongoDB = Database::instance();
$mongoDB->setCredentials();
$playersCollection = $mongoDB->connectToTable('player_classes');
$activeTeamsCollection = $mongoDB->connectToTable('active_teams');
$managersCollection = $mongoDB->connectToTable('managers');
$transferMarketCollection = $mongoDB->connectToTable('transfer_market');
$clubsCollection = $mongoDB->connectToTable('clubs');
$countriesCollection = $mongoDB->connectToTable('countries');

switch ($functionName)
{
	case 'generateRandomPlayer':
		$randomPlayer = $mongoDB->getPlayer($playersCollection);
		echo $randomPlayer;
		break;

	case 'getStartingEleven':
		$team = $mongoDB->getStartingEleven($activeTeamsCollection, $playersCollection, $myManagerID);
		echo json_encode($team);
		break;

	case 'getPlayOpponents':
		$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);

		$itemsArray = [];
		foreach($awayManagers as $managerInfo)
		{
			$tempArray = [
				"manager_id" => $managerInfo["manager_id"],
				"team_name" => $managerInfo["team_name"],
	           	"overall" => round($managerInfo["overall"])
			];

			$itemsArray[] = $tempArray;
		}

		echo json_encode($itemsArray);
		break;

	case 'playMatch':
		$awayManagerID = $request[0];
		$homeManager = $mongoDB->getMyTeamInfo($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);
		$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);

		$match = new Match($homeManager[$myManagerID], $awayManagers[$awayManagerID], $managersCollection);
		echo $match->simulatePreview();
		echo $match->simulateMatch();
		break;

	case 'getAllPlayers':
		$includeStartingEleven = True;
		$team = $mongoDB->getAllPlayers($myManagerID, $managersCollection, $playersCollection, $activeTeamsCollection, $includeStartingEleven);
		echo json_encode($team);
		break;

	case 'getBenchedPlayers':
		$includeStartingEleven = False;
		$team = $mongoDB->getAllPlayers($myManagerID, $managersCollection, $playersCollection, $activeTeamsCollection, $includeStartingEleven);
		echo json_encode($team);
		break;

	case 'replacePlayer':
		$activePlayerPosition = $request[0];
		$benchedPlayerID = $request[1];
		$mongoDB->replacePlayer($myManagerID, $activePlayerPosition, $benchedPlayerID, $managersCollection, $playersCollection, $activeTeamsCollection);
		break;

	case 'getMyStats':
		$myStats = $mongoDB->getMyStats($myManagerID, $managersCollection, $clubsCollection);
		echo json_encode($myStats);
		break;

	case 'getTransferMarketPlayers':
		$marketDetails = $mongoDB->getTransferMarketPlayers($myManagerID, $transferMarketCollection, $playersCollection);
		echo json_encode($marketDetails);
		break;

	case 'getSellingPlayers':
		$sellingPlayers = $mongoDB->getSellingPlayers($myManagerID, $playersCollection, $managersCollection);
		echo json_encode($sellingPlayers);
		break;

	case 'sellPlayer':
		$playerID = $request[0];
		$coins = $request[1];
		$mongoDB->sellPlayer($playerID, $coins, $myManagerID, $managersCollection, $playersCollection, $transferMarketCollection, $activeTeamsCollection);
		break;

	case 'buyPlayer':
		$playerID = $request[0];
		$ownerID = $request[1];
		$marketDetails = $mongoDB->buyPlayer($playerID, $ownerID, $myManagerID, $managersCollection, $transferMarketCollection, $playersCollection, $activeTeamsCollection);
		break;

	case 'createManager':
		$teamName = $request[0];
		$favouriteTeam = $request[1];
		$mongoDB->createNewManager($myManagerID, $favouriteTeam, $teamName, $playersCollection, $managersCollection, $activeTeamsCollection);
		break;	
}
?>