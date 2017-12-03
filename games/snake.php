<!DOCTYPE HTML>
<html>

<head>
  <title>Cormorant Games</title>
  <meta name="description" content="Web HTML Games" />
  <meta name="keywords" content="Web Game, HTML, Javascript" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="../style/style.css" />
  <link rel="stylesheet" href="jquery-ui.css">
  <script src="jquery.min.js" type="text/javascript"></script>
  <script src="snake.js" type="text/javascript"></script>
</head>

<body> 
  <div id="main">
    <div id="header">
      <div id="logo">
         <h1><span class="logo_colour">cormorant</span></h1>
          <h2>&nbsp; &nbsp; &nbsp; Web HTML Games</h2>
      </div>
    </div>
    <div id="site_content">
      <div id="content">
		<h2>
			Move the snake using W, A, S, D.
		</h2>
        <canvas id="canvas" width="450px" height="450px"></canvas>
		
		<form action="../database/gameRatingDB.php" method="post">
          <div class="form_settings">
		    <p>
			<span>Rate our game</span>
			<select id="id" name="rating">
				<option value="1">1 star</option>
				<option value="2">2 stars</option>
				<option value="3">3 stars</option>
				<option value="4">4 stars</option>
				<option value="5">5 stars</option>
			</select>
			</p>
			<input type="hidden" name="game_id" value="2" />
			<span>Current username</span><input type="text" name="username" value="
				<?php
					session_start();
					if(isset($_SESSION['logID']))
					{
						echo ''.$_SESSION['username'];
					}
					else
					{
						echo 'Login to submit rating';
					}
				?>
			" readonly/>
			<?php
				if(isset($_SESSION['logID']))
				{
					echo '<p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="game_submit_button" value="Submit rating" /></p>';
				}
			?>
		  </div>
		</form>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
	</div>
</body>
</html>