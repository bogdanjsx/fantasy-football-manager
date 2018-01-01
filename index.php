<!DOCTYPE HTML>
<html>
<head>
	<title>Fantasy football manager</title>
	<meta name="description" content="Fantasy football simulator" />
	<meta name="keywords" content="Web Game, HTML, Javascript, PHP, football, manager, simulator" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="./style/style.css" />
	<link rel="stylesheet" type="text/css" href="./style/fut-cards.css" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>
	<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.24.0/babel.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>

	<script type="text/babel" src="Card.jsx"></script>
	<script type="text/babel" src="PlayerList.jsx"></script>
	<script type="text/babel" src="AllPlayersList.jsx"></script>
	<script type="text/babel" src="ManagerList.jsx"></script>
	<script type="text/javascript" src="tabs.js"></script>

	<script>
		function goTo(site, callback) {
			if (!callback) {
			callback = () => {};
			}
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("site_content").innerHTML = this.responseText;
					callback();
				}
			};

			xmlhttp.open("GET", site, true);
			xmlhttp.send();
		}

		function setSelected(id) {
			document.getElementById(id).classList.add('selected');
			for(count = 1; count < 7; count ++) {
				if(count != id) {
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
				<li id=2><a onclick="goTo('my_team.php', myTeamTab);setSelected(2);">My team</a></li>
				<li id=3><a onclick="goTo('transfer_market.php', myPlayersTab);setSelected(3)">Transfer market</a></li>
				<li id=4><a onclick="goTo('weekly_challenge.php');setSelected(4)">Weekly challenge</a></li>
				<li id=5><a onclick="goTo('stats.php, statsTab');setSelected(5)">Stats</a></li>
				<li id=6><a onclick="goTo('my_account.php');setSelected(6)">My account</a></li>
				</ul>
			</div>
		</div>
		<div id="site_content">
			<div id="content">
				<?php
				session_start();

				if(isset($_SESSION['logID'])) {
					echo "<p>You are logged in as " . $_SESSION['username'] . ".</p>";
					echo "<p>To logout from your account acces this <a href=\"database/logout.php\">link</a>.</p>";
				}
				else {
					echo "<p>You are not logged in! To register or log into your account access the Login page.</p>";
				}
				?>
			</div>
		</div>
		<div id="footer" style="cursor:pointer">
			<p>
				<a href="index.php">Play</a> | 
				<a onclick="goTo('my_team.php', myTeamTab)">My team</a> | 
				<a onclick="goTo('transfer_market.php', myPlayersTab)">Transfer market</a> | 
				<a onclick="goTo('weekly_challenge.php')">Weekly challenge</a> | 
				<a onclick="goTo('stats.php', statsTab)">Stats</a> | 
				<a onclick="goTo('my_account.php')">My account</a>
			</p>
			<p>Copyright &copy; Fantasy football manager</p>
		</div>
	</div>
</body>
</html>