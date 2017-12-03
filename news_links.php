<!DOCTYPE HTML>
<html>

<head>
  <title>Cormorant Games</title>
  <meta name="description" content="Web HTML Games" />
  <meta name="keywords" content="Web Game, HTML, Javascript" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
</head>

<body>
  <div id="main">
    <div id="site_content">
      <div id="content">
		<?php
			require 'database/User.php';
			$users = User::getAllRank2Users();
			
			if(count($users) != 0)
			{
				echo "<h1>List of important users</h1>";
			}
			foreach($users as $user)
			{
				echo "<p>" . $user->username . "</p>";
			}
		?>
        <h1>News</h1>
			<h2>First 3 games have been released!</h2>
				<h5>May 1st, 2017</h5>
					<p>Out first 3 games have been released, written in HTML5: Pong, Snake and Flappy Birds!</p>
			<h2>First games soon to be released!</h2>
				<h5>April 8th, 2017</h5>
					<p>Come back next month to play our first 3 games: Pong, Snake and Flappy Birds!</p>
			<p></p>
			<h2>Website Launched!</h2>
				<h5>April 1st, 2017</h5>
					<p>2017 sees the launch of our website. Take a look around and let us know what you think.</p>
			
		<h1>Our gaming partners</h1>
        <ul>
			<li><a href="https://www.classicgame.com/">Classic Game</a></li>
			<li><a href="https://www.retrogamer.net/">Retro Gamer</a></li>
			<li><a href="https://www.classicdosgames.com/">Classic DOS Games</a></li>
			<li><a href="http://www.classicgamesarcade.com/">Classic Games Arcade</a></li>
			<li><a href="http://game-oldies.com/">Game Oldies</a></li>
        </ul>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
  </div>
</body>
</html>