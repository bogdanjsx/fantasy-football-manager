<?php

/*
	Simulates a match between 2 managers and returns a random score (for the moment)
	Input is the array info of the home and away manager
	Home manager is the manager who is currently playing and issued the challenge
*/
class Match
{
	private $homeManagerArray, $awayManagerArray;

	private $homeGoals, $awayGoals;

	private $bias;

	public function __construct($homeManagerArray, $awayManagerArray, $managersCollection)
	{
		$this->homeManagerArray = $homeManagerArray;
		$this->awayManagerArray = $awayManagerArray;
		$this->homeGoals = 0;
		$this->awayGoals = 0;

		$this->managersCollection = $managersCollection;

		$this->bias = 0;

		$this->getBias();
	}

	//Gets bias and bookmakers' prediction
	public function simulatePreview()
	{
		$this->bias = $this->getBias();
		$this->publishMatchPreview();
	}

	//Simulates the match and returns a score
	public function simulateMatch()
	{
		$this->getRandomScore();
		$this->updateRecord();

		$this->publishMatchReport();
		$this->publishScore();
	}

	
	private function getBias()
	{
		$bias = ($this->getOverallBias() +
			$this->getAttackingBias() +
			$this->getMidBias() +
			$this->getDefendingBias() +
			$this->getGKBias() +
			2 * $this->getChemistryBias()) / 7;

		return $bias;
	}
		
	/*
	Bias between -100 and +100 (certain defeat or win)
	Worst rating possible is 46, best is 91
	Take the difference, double it and add or substract 10
	*/
	private function getOverallBias()
	{
		$homeTeamOverall = $this->homeManagerArray['overall'];
		$awayTeamOverall = $this->awayManagerArray['overall'];

		$overallDifference = $homeTeamOverall - $awayTeamOverall;
		$overallDifference *= 2;

		if($overallDifference > 0)
		{
			$overallDifference += 10;
		}
		else if($overallDifference < 0)
		{
			$overallDifference -= 10;
		}

		return $overallDifference;
	}

	//Chemistry influences the bias the most
	private function getChemistryBias()
	{
		$homeTeamChemistry = $this->homeManagerArray['chemistry'];
		$awayTeamChemistry = $this->awayManagerArray['chemistry'];

		$overallDifference = $homeTeamChemistry - $awayTeamChemistry;
		return $overallDifference;
	}

	//Influenced by PACE and SHOOTING
	private function getAttackingBias()
	{
		$homeStartingEleven = $this->homeManagerArray['starting_eleven'];
		$awayStartingEleven = $this->awayManagerArray['starting_eleven'];

		$homeAttackers = [ 
			json_decode($homeStartingEleven["LST"]["player"]),
			json_decode($homeStartingEleven["RST"]["player"])
		];

		$awayAttackers = [ 
			json_decode($awayStartingEleven["LST"]["player"]),
			json_decode($awayStartingEleven["RST"]["player"])
		];

		$homeStats = 0;
		$awayStats = 0;

		foreach($homeAttackers as $player)
		{
			$vars = get_object_vars($player);
			$homeStats += $vars["pace"] + $vars["shooting"];
		}

		foreach($awayAttackers as $player)
		{
			$vars = get_object_vars($player);
			$awayStats += $vars["pace"] + $vars["shooting"];
		}

		$overallDifference = ($homeStats - $awayStats) / 2;

		return $overallDifference;
	}

	//Influenced by DRIBBLING and PASSING; Wingers also influence by PACE and SHOOTING 
	private function getMidBias()
	{
		$homeStartingEleven = $this->homeManagerArray['starting_eleven'];
		$awayStartingEleven = $this->awayManagerArray['starting_eleven'];

		$homeMidfielders = [ 
			"LM" => json_decode($homeStartingEleven["LM"]["player"]),
			"LCM" => json_decode($homeStartingEleven["LCM"]["player"]),
			"RCM" => json_decode($homeStartingEleven["RCM"]["player"]),
			"RM" => json_decode($homeStartingEleven["RM"]["player"])
		];

		$awayMidfielders = [ 
			"LM" => json_decode($awayStartingEleven["LM"]["player"]),
			"LCM" => json_decode($awayStartingEleven["LCM"]["player"]),
			"RCM" => json_decode($awayStartingEleven["RCM"]["player"]),
			"RM" => json_decode($awayStartingEleven["RM"]["player"])
		];

		$homeStats = 0;
		$awayStats = 0;

		foreach($homeMidfielders as $key => $player)
		{
			$vars = get_object_vars($player);
			$homeStats += $vars["dribbling"] + $vars["passing"];

			if(in_array($key, ["LM", "RM"]))
			{
				$homeStats += $vars["shooting"] + $vars["pace"];
			}
		}

		foreach($awayMidfielders as $key => $player)
		{
			$vars = get_object_vars($player);
			$awayStats += $vars["dribbling"] + $vars["passing"];

			if(in_array($key, ["LM", "RM"]))
			{
				$awayStats += $vars["shooting"] + $vars["pace"];
			}
		}

		$overallDifference = ($homeStats - $awayStats) / 3;

		return $overallDifference;
	}

	//Influenced by PHYSICAL and DEFENDING; Wingers also influence by PACE
	private function getDefendingBias()
	{
		$homeStartingEleven = $this->homeManagerArray['starting_eleven'];
		$awayStartingEleven = $this->awayManagerArray['starting_eleven'];

		$homeDefenders = [ 
			"LB" => json_decode($homeStartingEleven["LB"]["player"]),
			"LCB" => json_decode($homeStartingEleven["LCB"]["player"]),
			"RCB" => json_decode($homeStartingEleven["RCB"]["player"]),
			"RB" => json_decode($homeStartingEleven["RB"]["player"])
		];

		$awayDefenders = [ 
			"LB" => json_decode($awayStartingEleven["LB"]["player"]),
			"LCB" => json_decode($awayStartingEleven["LCB"]["player"]),
			"RCB" => json_decode($awayStartingEleven["RCB"]["player"]),
			"RB" => json_decode($awayStartingEleven["RB"]["player"])
		];

		$homeStats = 0;
		$awayStats = 0;

		foreach($homeDefenders as $key => $player)
		{
			$vars = get_object_vars($player);
			$homeStats += $vars["physical"] + $vars["defending"];

			if(in_array($key, ["LB", "RB"]))
			{
				$homeStats += $vars["pace"];
			}
		}

		foreach($awayDefenders as $key => $player)
		{
			$vars = get_object_vars($player);
			$awayStats += $vars["physical"] + $vars["defending"];

			if(in_array($key, ["LB", "RB"]))
			{
				$awayStats += $vars["pace"];
			}
		}

		$overallDifference = ($homeStats - $awayStats) / 2.5;

		return $overallDifference;
	}

	//Influenced by all goalkeeper stats
	private function getGKBias()
	{
		$homeStartingEleven = $this->homeManagerArray['starting_eleven'];
		$awayStartingEleven = $this->awayManagerArray['starting_eleven'];

		$homeGoalkeeper = [ 
			json_decode($homeStartingEleven["GK"]["player"])
		];

		$awayGoalkeeper = [ 
			json_decode($awayStartingEleven["GK"]["player"])
		];

		$homeStats = 0;
		$awayStats = 0;

		foreach($homeGoalkeeper as $player)
		{
			$vars = get_object_vars($player);
			$homeStats += $vars["gk handling"] + $vars["gk positioning"] +
					$vars["gk kicking"] + $vars["gk diving"] +
					$vars["gk speed"] + $vars["gk reflexes"];
		}

		foreach($awayGoalkeeper as $player)
		{
			$vars = get_object_vars($player);
			$awayStats += $vars["gk handling"] + $vars["gk positioning"] +
					$vars["gk kicking"] + $vars["gk diving"] +
					$vars["gk speed"] + $vars["gk reflexes"];
		}

		$overallDifference = ($homeStats - $awayStats) / 2;

		return $overallDifference;
	}

	//Algoritm cat de cat inteligent
	private function getRandomScore()
	{
		$randomNumber1 = mt_rand(0, 100);
		$randomNumber1 += $this->bias;

		//First half, cica
		if($randomNumber1 > 50)
		{
			$this->homeGoals += mt_rand(0, 2);
		}
		else
		{
			$this->awayGoals += mt_rand(0, 2);
		}

		$randomNumber2 = mt_rand(5, 100);
		$randomNumber2 += $this->bias;

		//Second half, cica; avantaj gazdele, doar pune presiune publicul, no?
		if($randomNumber2 > 50)
		{
			$this->homeGoals += mt_rand(0, 3);
			$this->awayGoals += mt_rand(0, 1);
		}
		else
		{
			$this->homeGoals += mt_rand(0, 1);
			$this->awayGoals += mt_rand(0, 3);
		}

		//Fergie time
		$hearthstoneDice = mt_rand(0, 6);

		//DAU CU ZARU 6-6
		if($hearthstoneDice > 2)
		{
			$this->homeGoals += mt_rand(0, 1);
		}
		else
		{
			$this->awayGoals += mt_rand(0, 1);
		}
	}

	private function publishMatchPreview()
	{
		$previewText = "Bookmakers predict ";

		if($this->bias >= 0)
		{
			$previewText .= $this->homeManagerArray['team_name'];
		}
		else
		{
			$previewText .= $this->awayManagerArray['team_name'];
		}

		$previewText .= " to have the edge today." . "<br>";

		echo $previewText;
	}

	private function publishMatchReport()
	{
		$totalGoals = $this->homeGoals + $this->awayGoals;

		$randomReport = [
			"minutes" => [],
			"scorers" => [
				"home" => [],
				"away" => []
			]
		];

		//Get random minutes
		for ($i = 0; $i < $totalGoals; $i ++) 
		{
		    $randomReport["minutes"][] = mt_rand(0, 90);
		}

		sort($randomReport["minutes"]);

		if($this->homeGoals)
		{
			$homeStartingEleven = $this->homeManagerArray['starting_eleven'];

			//Get random goalscorers for each side
			for($i = 0; $i < $this->homeGoals; $i ++)
			{
				$randomNumber = mt_rand(0, 100);

				//Striker or winger scored
				if($randomNumber > 40)
				{
					$positions = ["LST", "RST", "LM", "RM"];
					$randomPosition = mt_rand(0, 3);
				}
				//Central middlefielder scored
				else if ($randomNumber > 15)
				{
					$positions = ["LCM", "RCM"];
					$randomPosition = mt_rand(0, 1);
				}
				//Defender scored
				else
				{
					$positions = ["LB", "LCB", "RCB", "RB"];
					$randomPosition = mt_rand(0, 3);
				}

				$player = $homeStartingEleven[$positions[$randomPosition]]["player"];
				$decodedPlayer = json_decode($player);
				$vars = get_object_vars($decodedPlayer);
				$goalScorerName = $vars["name"];
				
				$randomReport["scorers"]["home"][] = $goalScorerName;
			}
		}

		if($this->awayGoals)
		{
			$awayStartingEleven = $this->awayManagerArray['starting_eleven'];

			//Get random goalscorers for each side
			for($i = 0; $i < $this->awayGoals; $i ++)
			{
				$randomNumber = mt_rand(0, 100);

				//Striker or winger scored
				if($randomNumber > 40)
				{
					$positions = ["LST", "RST", "LM", "RM"];
					$randomPosition = mt_rand(0, 3);
				}
				//Central middlefielder scored
				else if ($randomNumber > 15)
				{
					$positions = ["LCM", "RCM"];
					$randomPosition = mt_rand(0, 1);
				}
				//Defender scored
				else
				{
					$positions = ["LB", "LCB", "RCB", "RB"];
					$randomPosition = mt_rand(0, 3);
				}

				$player = $awayStartingEleven[$positions[$randomPosition]]["player"];
				$decodedPlayer = json_decode($player);
				$vars = get_object_vars($decodedPlayer);
				$goalScorerName = $vars["name"];
				
				$randomReport["scorers"]["away"][] = $goalScorerName;
			}
		}
		
		$reportText = "";

		$tempHomeGoals = 0;
		$tempAwayGoals = 0;

		for($i = 0; $i < $totalGoals; $i ++)
		{
			//All home goals scored
			if($tempHomeGoals == $this->homeGoals)
			{
				$tempAwayGoals ++;
				$reportText .= $randomReport["minutes"][$i] . "' " . $tempHomeGoals . "-" . $tempAwayGoals;

				$reportText .= " " . array_pop($randomReport["scorers"]["away"]) . "<br>";
			}
			//All away goals scored
			else if($tempAwayGoals == $this->awayGoals)
			{
				$tempHomeGoals ++;
				$reportText .= $randomReport["minutes"][$i] . "' " . $tempHomeGoals . "-" . $tempAwayGoals;

				$reportText .= " " . array_pop($randomReport["scorers"]["home"]) . "<br>";
			}
			//Both teams need to score
			else
			{
				$randomNumber = mt_rand(0, 100);

				//Home scores
				if($randomNumber > 50)
				{
					$tempHomeGoals ++;
					$reportText .= $randomReport["minutes"][$i] . "' " . $tempHomeGoals . "-" . $tempAwayGoals;

					$reportText .= " " . array_pop($randomReport["scorers"]["home"]) . "<br>";
				}
				//Away scores
				else
				{
					$tempAwayGoals ++;
					$reportText .= $randomReport["minutes"][$i] . "' " . $tempHomeGoals . "-" . $tempAwayGoals;

					$reportText .= " " . array_pop($randomReport["scorers"]["away"]) . "<br>";
				}
			}
		}

		echo $reportText;
	}

	private function publishScore()
	{
		$homeTeamName = $this->homeManagerArray['team_name'];
		$awayTeamName = $this->awayManagerArray['team_name'];

		$score = $homeTeamName . " " . $this->homeGoals . " - " . $this->awayGoals . " " . $awayTeamName;

		echo $score;
	}

	private function updateRecord()
	{
		$homeRecord = $this->homeManagerArray['record'];
		$awayRecord = $this->awayManagerArray['record'];

		$homeCoins = $this->homeManagerArray['coins'];
		$awayCoins = $this->awayManagerArray['coins'];

		if($this->homeGoals > $this->awayGoals)
		{
			$homeRecord['wins'] ++;
			$awayRecord['losses'] ++;
			$homeCoins += 200 * ($this->homeGoals - $this->awayGoals);
		}
		else if($this->homeGoals < $this->awayGoals)
		{
			$homeRecord['losses'] ++;
			$awayRecord['wins'] ++;
			$awayCoins += 200 * ($this->awayGoals - $this->homeGoals);
		}
		else
		{
			$homeRecord['draws'] ++;
			$awayRecord['draws'] ++;

			$homeCoins += 100;
			$awayCoins += 100;
		}

		$homeGoalDifference = $this->homeManagerArray['goal_difference'];
		$awayGoalDifference = $this->awayManagerArray['goal_difference'];

		$homeGoalDifference['goals_for'] += $this->homeGoals;
		$homeGoalDifference['goals_against'] += $this->awayGoals;

		$awayGoalDifference['goals_against'] += $this->homeGoals;
		$awayGoalDifference['goals_for'] += $this->awayGoals;



		$this->managersCollection->updateOne(
			['_id' => $this->homeManagerArray['manager_id']],
			['$set' => [
						'record' => $homeRecord,
						'goal_difference' => $homeGoalDifference,
						'coins' => $homeCoins
					]
			]
		);

		$this->managersCollection->updateOne(
			['_id' => $this->awayManagerArray['manager_id']],
			['$set' => [
						'record' => $awayRecord,
						'goal_difference' => $awayGoalDifference,
						'coins' => $awayCoins
					]
			]
		);
	}
}
?>