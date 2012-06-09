<?php

require_once('../formspring.class.php');

$fs = new Formspring('EugeneBelov');

// Set limit option to 5 responses:
$fs->setOpt('limit', 5);

// Getting last 5 responses (limit value):
$items = $fs->get();

// Show them:
foreach ($items as $item)
{
	echo	'<h2>'.$item->question.'</h2>'.
			$item->answer.'<br />
			<small>'.date('d.m.Y', strtotime($item->time)).'</small><hr />';
}

?>