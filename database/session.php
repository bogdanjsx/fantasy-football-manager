<?php
require_once 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=cormorant');
ORM::configure('username', 'root');
ORM::configure('password', '');

//Redirect to given url and post the message
function redirect($url, $message)
{
	echo '<script type="text/javascript">';
	echo "window.location.href = '" . $url . "';";
	echo 'alert("' . $message . '");'; 
	echo '</script>';
	exit;
}
?>