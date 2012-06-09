<?php

require_once('../formspring.class.php');

$fs = new Formspring('EugeneBelov');

// Getting last 20 records:
$items = $fs->get();

// Show them:
foreach ($items as $item)
{
	echo	'<h2>'.$item->question.'</h2>'.
			$item->answer.'<br />
			<small>'.date('d.m.Y', strtotime($item->time)).'</small><hr />';
}

// --- Get more records -------

// Id of last record:
$lastId = $fs->lastId();

// Get records before this id (older responses):
$items = $fs->get($lastId);

// Show them:
foreach ($items as $item)
{
	echo	'<h2>'.$item->question.'</h2>'.
			$item->answer.'<br />
			<small>'.date('d.m.Y', strtotime($item->time)).'</small><hr />';
}

?>