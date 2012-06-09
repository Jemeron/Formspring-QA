<?php

require_once('../formspring.class.php');

$fs = new Formspring('EugeneBelov');

// Options array:
$opt = array(
			// Cached result of Formspring::getToken('EugeneBelov') method:
			'token'			=> 'c1da1f1a2088b713bb66d6a14d4d9ca6',
			// In which User-Agent (browser) we got this token:
			'user_agent'	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5'
		);

// Set the options as array:
$fs->setOpt($opt);

// Text of the question (max 255 chars):
$question = 'Does it works?';

// Ask it:
$fs->ask($question);

?>