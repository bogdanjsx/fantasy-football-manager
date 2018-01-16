<?php
require_once 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=manager');
ORM::configure('username', 'root');
ORM::configure('password', '');

ORM::get_db()->exec('DROP TABLE IF EXISTS users;');
ORM::get_db()->exec(
        'CREATE TABLE users (' .
        'id INTEGER PRIMARY KEY AUTO_INCREMENT, ' .
        'username VARCHAR(50), ' .
        'password VARCHAR(50), ' .
		'email VARCHAR(100), ' .
        'managerID INTEGER' .
        ')'
    );