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

	public function __construct($homeManagerArray, $awayManagerArray)
	{
		$this->homeManagerArray = $homeManagerArray;
		$this->awayManagerArray = $awayManagerArray;
		$this->homeGoals = 0;
		$this->awayGoals = 0;
	}

	//Simulates the match and returns a score
	public function simulateMatch()
	{
		$this->getRandomScore();

		return $this->publishScore();
	}

	/*
		Bias between -100 and +100 (certain defeat or win)
		Worst rating possible is 46, best is 91
		Take the difference, double it and add or substract 10

		IMPORTANT: it also must be influenced by team chemistry, not only by overall
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

	//Algoritm cat de cat inteligent
	private function getRandomScore()
	{
		$randomNumber1 = mt_rand(0, 100);
		$randomNumber1 += $this->getOverallBias();

		//First half, cica
		if($randomNumber1 > 50)
		{
			$this->homeGoals += mt_rand(0, 2);
		}
		else
		{
			$this->awayGoals += mt_rand(0, 2);
		}

		$randomNumber2 = mt_rand(0, 95);
		$randomNumber2 += $this->getOverallBias();

		//Second half, cica; avantaj gazdele, doar pune presiune publicul, no?
		if($randomNumber2 > 50)
		{
			$this->homeGoals += mt_rand(0, 3);
		}
		else
		{
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

	private function publishScore()
	{
		$homeTeamName = $this->homeManagerArray['team_name'];
		$awayTeamName = $this->awayManagerArray['team_name'];

		$score = $homeTeamName . " " . $this->homeGoals . " - " . $this->awayGoals . " " . $awayTeamName;

		return $score;
	}
}
?>