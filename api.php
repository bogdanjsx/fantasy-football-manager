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
		// Transform object to list
		/*
		var awayManagers = getPlayOpponents();
 -    var count = awayManagers.length;
 -
 -    var itemsArray = [];
 -    
 -    for(const managerID in awayManagers)
 -    {
 -        var tempArray = {
 -            "manager_id" : awayManagers[managerID]["manager_id"],
 -            "team_name" : awayManagers[managerID]["team_name"],
 -            "overall" : Math.round(awayManagers[managerID]["overall"])
 -        };
 -
 -       itemsArray.push(tempArray);
 -       
 -    }
 -
 -     var reactVar = React.createElement(ManagerList, {"items" : itemsArray});
 -     ReactDOM.render(reactVar, document.getElementById("content"));
		*/
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
		$awayManagerID = array_shift($request) + 0;
		$homeManager = $mongoDB->getMyTeamInfo($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);
		$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);

		$match = new Match($homeManager[$myManagerID], $awayManagers[$awayManagerID], $managersCollection);
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

	//TODO
	case 'replacePlayer':
		$startingIndex = array_shift($request) + 0;
		$benchedPlayerID = array_shift($request) + 0;
		break;
}
?>