<?php
ob_start();
session_start();

require_once 'database/session.php';
//Check if user is already logged in and redirect to home page
if(isset($_SESSION['logID']))
{
	redirect("index.php", "You are already logged in!");
}
?>
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
	</script>
</head>

<body>
  <div id="main">
    <div id="site_content">
      <div id="content">
        <h1>Login to our site</h1>
        <form action="database/login.php" method="post">
          <div class="form_settings">
            <p><span>Username</span><input type="text" name="user" value="" pattern=".{6,}" title="Username must contain six or more characters" required/></p>
            <p><span>Password</span><input type="password" name="pw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required></p>
            <span>&nbsp;</span><input class="submit" type="submit" name="login_button" value="Login" />
          </div>
        </form>
		
		<h2><a style="cursor:pointer" onclick="goTo('register.html')">Don't have an account? Register now!</a></h2>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
  </div>
</body>
</html>
