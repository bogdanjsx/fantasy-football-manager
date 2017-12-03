<!DOCTYPE HTML>
<html>

<head>
  <title>Cormorant Games</title>
  <meta name="description" content="Web HTML Games" />
  <meta name="keywords" content="Web Game, HTML, Javascript" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/style.css" />
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
		  
		  for(count = 1; count < 5; count ++)
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
         <h1><span class="logo_colour">cormorant</span></h1>
          <h2>&nbsp; &nbsp; &nbsp; Web HTML Games</h2>
      </div>
      <div id="menubar">
        <ul id="menu" style="cursor:pointer">
          <li id=1 class="selected"><a href='index.php' onclick="setSelected(1)">Home</a></li>
          <li id=2><a onclick="goTo('games.html');setSelected(2)">Games</a></li>
          <li id=3><a onclick="goTo('news_links.php');setSelected(3)">News & Links</a></li>
          <li id=4><a onclick="goTo('login.php');setSelected(4)">Login</a></li>
          <li id=5><a onclick="goTo('contact.php');setSelected(5)">Contact Us</a></li>
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div id="content">
		<?php
			session_start();
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
        <h1>Welcome to Cormorant Games!</h1>
        <p>Play the best free Classic and Retro games online with Pacman, Tetris, Pinball, Snake, Golf and many more classic games.</p>
		<p>The games are playable on desktop, tablet and mobile (Android, iOS, Windows Mobile). Like us on Facebook or follow us on Twitter to stay up to date of our new classic games.</p>
        <p>You can play either as a guest, or as a logged user and rate our games and give us feedback!<p>
		<p>We have 3 games planned to release, but stay tuned for other updates. If you want you can contact us and sugest what games you like and we'll code them.<p>
		<h2>Current games list</h2>
        <p>Choose from any of these exciting games and play them from the Games tab:</p>
        <ul>
          <li>Pong</li>
          <li>Snake</li>
          <li>Flappy Birds</li>
        </ul>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
    <div id="footer" style="cursor:pointer">
      <p><a href="index.php">Home</a> | <a onclick="goTo('games.html')">Games</a> | <a onclick="goTo('news_links.php')">News & Links</a> | <a onclick="goTo('login.php')">Login</a> | <a onclick="goTo('contact.php')">Contact Us</a></p>
      <p>Copyright &copy; cormorant</p>
	</div>
	</div>
</body>
</html>