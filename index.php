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
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/babel" src="players.jsx"></script>

  <script>
	  function goTo(site, callback)
	  {
			if (!callback) {
				 callback = () => {};
			}
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					document.getElementById("site_content").innerHTML = this.responseText;
					callback();
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
          <li id=2><a onclick="goTo('my_team.php', renderTeam);setSelected(2);">My team</a></li>
          <li id=3><a onclick="goTo('transfer_market.php', openDialog);setSelected(3)">Transfer market</a></li>
          <li id=4><a onclick="goTo('weekly_challenge.php');setSelected(4)">Weekly challenge</a></li>
          <li id=5><a onclick="goTo('stats.php');setSelected(5)">Stats</a></li>
          <li id=6><a onclick="goTo('my_account.php');setSelected(6)">My account</a></li>
          
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div id="content">
		<?php
			session_start();
			require 'mongo/Database.php';
			require 'mongo/Match.php';
			
			if(isset($_SESSION['logID']))
			{
				echo "<p>You are logged in as " . $_SESSION['username'] . ".</p>";
				echo "<p>To logout from your account acces this <a href=\"database/logout.php\">link</a>.</p>";
			}
			else
			{
				echo "<p>You are not logged in! To register or log into your account access the Login page.</p>";
			}

			$mongoDB = Database::instance();
			$mongoDB->setCredentials();
			$playersCollection = $mongoDB->connectToTable('player_classes');
			$activeTeamsCollection = $mongoDB->connectToTable('active_teams');
			$managersCollection = $mongoDB->connectToTable('managers');

			//The current manager that is playing, later it will be returned from $_SESSION['managerID']
			$managerID = 41; //41 sau 42 sau 420

			//The challenger for the match (as chosen from the match list)
			$awayManagerID = 42;


			/* Get all players or benched ones for a manager
			$includeStartingEleven = False;
			$team = $mongoDB->getAllPlayers($managerID, $managersCollection, $playersCollection, $activeTeamsCollection, $includeStartingEleven);
			echo var_dump($team);
			*/


			//	Get all managers except current manager
			$awayManagers = $mongoDB->getAllManagers($managersCollection, $playersCollection, $activeTeamsCollection, $managerID);
			//echo var_dump($awayManagers);
			
			
			/*
				Get current manager's team

			*/
			$homeManager = $mongoDB->getMyTeamInfo($managersCollection, $playersCollection, $activeTeamsCollection, $managerID);
			echo var_dump($homeManager);

			//Simulate match between two teams
			$match = new Match($homeManager[$managerID], $awayManagers[$awayManagerID], $managersCollection);
			echo $match->simulateMatch();
		?>
		
		<script type="text/Javascript"  async=false>

			function generateRandomPlayer(count)
			{
				var randomPlayerList = [];

				for(var i = 0; i < count; i ++)
				{
					var randomPlayer = <?php
						$randomPlayer = $mongoDB->getPlayer($playersCollection);
						echo $randomPlayer;
						?>

					randomPlayerList.push(randomPlayer);
				}

				return randomPlayerList;
			}
			
			
			function getStartingEleven()
			{
				var startingEleven = <?php
					$team = $mongoDB->getStartingEleven($activeTeamsCollection, $playersCollection, $managerID);
					echo json_encode($team);
					?>

				return startingEleven;
			}
			

 		 </script>
		<p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p>
		<p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
    <div id="footer" style="cursor:pointer">
      <p><a href="index.php">Play</a> | <a onclick="goTo('my_team.php')">My team</a> | <a onclick="goTo('transfer_market.php')">Transfer market</a> | <a onclick="goTo('weekly_challenge.php')">Weekly challenge</a> | <a onclick="goTo('stats.php')">Stats</a> | <a onclick="goTo('my_account.php')">My account</a></p>
      <p>Copyright &copy; Fantasy football manager</p>
		</div>
	</div>
</body>
</html>