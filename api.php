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
		$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);
		echo json_encode($awayManagers);
		break;

	case 'playMatch':
		$awayManagerID = array_shift($request) + 0;
		$homeManager = $mongoDB->getMyTeamInfo($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);
		$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $myManagerID);

		$match = new Match($homeManager[$myManagerID], $awayManagers[$awayManagerID], $managersCollection);
		echo $match->simulateMatch();
		break;

	case 'replacePlayer':
		$startingIndex = array_shift($request) + 0;
		$benchedPlayerID = array_shift($request) + 0;
		break;
}
?>