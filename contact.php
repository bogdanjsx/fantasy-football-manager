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
        <h1>Contact Us</h1>
        <p>Give us feedback for our site, especially our games. Also suggest what kind of new games would you like to see!</p>
        <form action="database/contactDB.php" method="post">
          <div class="form_settings">
            <p><span>Name</span><input class="contact" type="text" name="contact_name" value="" required/></p>
            <p><span>Email Address</span><input class="contact" type="email" name="contact_email" value="
				<?php
					session_start();
					if(isset($_SESSION['logID']))
					{
						echo ''.$_SESSION['email'];
					}
				?>
			" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="E-mail must be in the following order: characters@characters.domain" required/></p>
            <p><span>Message</span><textarea class="contact textarea" rows="8" cols="50" name="contact_feedback" required></textarea></p>
            <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="contact_button" value="Submit" /></p>
          </div>
        </form>
		<?php
			require 'database/Feedback.php';
			
			$messages = Feedback::getMessages();
			
			if(count($messages) != 0)
			{
				echo "<h1>Most recent messages</h1>";
			}
			
			foreach($messages as $message)
			{
				echo "<p>" . $message->name . "(" . $message->email . "): " . $message->text . "</p>";
			}
		?>
		<p></p><p></p><p></p><p></p><p></p><p></p>
      </div>
    </div>
	</div>
</body>
</html>
