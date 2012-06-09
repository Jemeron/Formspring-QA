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

// Total number of responses:
echo 'Total: '.$fs->total();

?>