<?php
require_once 'session.php';

session_start();
unset($_SESSION['logID']);
unset($_SESSION['username']);
unset($_SESSION['email']);
session_unset();
session_destroy();
redirect("../index.php", "Succesfully logged out!");
?>