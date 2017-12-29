<?php
function bsonUnserialize($map)
{
	$array = [];

    foreach ( $map as $k => $value )
    {
        $array[$k] = $value;
    }

    return $array;
}

function getBenchedPlayers($allPlayers, $startingEleven)
{
	$benchedPlayers = [];
	
	foreach($allPlayers as $player)
	{
		if(!in_array($player, $startingEleven))
		{
			$benchedPlayers[] = $player;
		}
	}

	return $benchedPlayers;
}
?>