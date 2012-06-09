<?php

require_once('../formspring.class.php');

$fs = new Formspring('EugeneBelov');

// Get token:
$token = Formspring::getToken('EugeneBelov');

// Set the option:
$fs->setOpt('token', $token);

// Text of the question (max 255 chars):
$question = 'Does it works?';

// Ask it:
$res = $fs->ask($question);

// Handle errors:
if (! $res)
{
	// Display error message:
	echo $res->error();
}
else
{
	// Id of new question:
	echo $res;
}

?>