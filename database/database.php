<?php
require_once 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=cormorant');
ORM::configure('username', 'root');
ORM::configure('password', '');

ORM::get_db()->exec('DROP TABLE IF EXISTS users;');
ORM::get_db()->exec(
        'CREATE TABLE users (' .
        'id INTEGER PRIMARY KEY AUTO_INCREMENT, ' .
        'username VARCHAR(50), ' .
        'password VARCHAR(50), ' .
		'email VARCHAR(100), ' .
        'rank TINYINT(2)' .
        ')'
    );
	
ORM::get_db()->exec('DROP TABLE IF EXISTS feedback;');
ORM::get_db()->exec(
        'CREATE TABLE feedback (' .
        'id INTEGER PRIMARY KEY AUTO_INCREMENT, ' .
		'name VARCHAR(50), ' .
        'text TEXT, ' .
		'email VARCHAR(100)' .
		')'
    );

ORM::get_db()->exec('DROP TABLE IF EXISTS game_rating;');
ORM::get_db()->exec(
        'CREATE TABLE game_rating (' .
        'id INTEGER PRIMARY KEY AUTO_INCREMENT, ' .
        'game_id INT(3), ' .
		'rating TINYINT(2), ' .
		'username VARCHAR(100)' .
		')'
    );