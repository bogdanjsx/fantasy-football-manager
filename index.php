<!DOCTYPE HTML>
<html>

<head>
  <title>Fantasy football manager</title>
  <meta name="description" content="Fantasy football simulator" />
  <meta name="keywords" content="Web Game, HTML, Javascript, PHP, football, manager, simulator" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="./style/style.css" />
  <link rel="stylesheet" type="text/css" href="./style/fut-cards.css" />
 	
  <script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.24.0/babel.js"></script>
  <script type="text/babel" src="players.jsx"></script>

  <script>
	  function goTo(site)
	  {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("site_content").innerHTML = this.responseText;
			}
		};
		
		xmlhttp.open("GET", site, true);
		xmlhttp.send();
	  }
	  
	  function setSelected(id)
	  {
		  document.getElementById(id).classList.add('selected');
		  
		  for(count = 1; count < 7; count ++)
		  {
			  if(count != id)
			  {
				  document.getElementById(count).classList.remove('selected');
			  }
		  }
	  }
  </script>
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
         <h1><span class="logo_colour">Fantasy football manager</span></h1>
          <h2>&nbsp; &nbsp; &nbsp; Football simulation web game</h2>
      </div>
      <div id="menubar">
        <ul id="menu" style="cursor:pointer">
          <li id=1 class="selected"><a href='index.php' onclick="setSelected(1)">Play</a></li>
          <li id=2><a onclick="goTo('my_team.php');setSelected(2)">My team</a></li>
          <li id=3><a onclick="goTo('transfer_market.php');setSelected(3)">Transfer market</a></li>
          <li id=4><a onclick="goTo('weekly_challenge.php');setSelected(4)">Weekly challenge</a></li>
          <li id=5><a onclick="goTo('stats.php');setSelected(5)">Stats</a></li>
          <li id=6><a onclick="goTo('my_account.php');setSelected(6)">My account</a></li>
          
        </ul>
      </div>
    </div>
    <div id="site_content">

	<div id="team">
      <div id="player0" class="playercard"/></div>
	  <div id="player1" class="playercard"/></div>
	  <div id="player2" class="playercard"/></div>
	  <div id="player3" class="playercard"/></div>
	  <div id="player4" class="playercard"/></div>
	  <div id="player5" class="playercard"/></div>
	  <div id="player6" class="playercard"/></div>
	  <div id="player7" class="playercard"/></div>
	  <div id="player8" class="playercard"/></div>
	  <div id="player9" class="playercard"/></div>
	  <div id="player10" class="playercard"/></div>
	</div>

      <div id="content">
		<?php
			session_start();
			require 'vendor/autoload.php';

			function bsonUnserialize($map)
			{
				$array = [];

			    foreach ( $map as $k => $value )
			    {
			        $array[$k] = $value;
			    }

			    return $array;
			}

			function getRandomPlayer($randomID)
			{
				$username = "alex";
				$password = "proiectsac";
				$database = "fantasy-football-manager";
				$client = new MongoDB\Client("mongodb://ds249025.mlab.com:49025/fantasy-football-manager", array("username" => $username, "password" => $password));

				try 
				{
				    $db = $client->getManager();
				}
				catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)
				{
				    echo $e->getMessage();
				}

				try
				{
					$database = $client->selectDatabase('fantasy-football-manager');
					$playerCollection = $database->selectCollection('player_classes');
				}
				catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)
				{
				    echo $e->getMessage();
				}

				$playersCount = $playerCollection->count();

				
				$cursor = $playerCollection->find([
					'_id' => $randomID
				]);

				foreach ($cursor as $randomPlayer) 
				{
				   $playerArray = bsonUnserialize($randomPlayer);
				};

				return json_encode($playerArray);
			}
			

			if(isset($_SESSION['logID']))
			{
				echo "<p>You are logged in as " . $_SESSION['username'] . ".</p>";
				echo "<p>To logout from your account acces this <a href=\"database/logout.php\">link</a></p>";
			}
			else
			{
				echo "<p>You are not logged in! To register or log into your account access the Login page</p>";
			}
		?>
		
		<script type="text/Javascript"  async=false>

			function generateRandomPlayer(count)
			{
				var randomPlayerList = [];

				for(var i = 0; i < count; i ++)
				{
					var randomPlayer = <?php
						$randomID = mt_rand(0, 100);
						$randomPlayer = getRandomPlayer($randomID);

						echo $randomPlayer;
						?>

					randomPlayerList.push(randomPlayer);
				}

				console.log(randomPlayerList);
				return randomPlayerList;

				/*
				var reactVar = React.createElement(PlayerCard, {"playerData" : randomPlayer});
				ReactDOM.render(reactVar, document.getElementById("players"));
				*/
			}
			/*
			document.addEventListener("DOMContentLoaded", function(event) {
				generateRandomPlayer();
			  });
			*/

 		 </script>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
    <div id="footer" style="cursor:pointer">
      <p><a href="index.php">Play</a> | <a onclick="goTo('my_team.php')">My team</a> | <a onclick="goTo('transfer_market.php')">Transfer market</a> | <a onclick="goTo('weekly_challenge.php')">Weekly challenge</a> | <a onclick="goTo('stats.php')">Stats</a> | <a onclick="goTo('my_account.php')">My account</a></p>
      <p>Copyright &copy; Fantasy football manager</p>
	</div>
	</div>
</body>
</html>