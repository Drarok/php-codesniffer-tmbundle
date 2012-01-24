<?php

$view = PHPCSView::factory('phplint_message');

if ((bool) $valid) {
	$view->set('class', 'success');
} else {
	$view->set('class', 'error');
}

foreach ($lint->getResults() as $message) {
	if (! trim($message)) {
		// Ignore blank lines.
		continue;
	}
	
	// Reset these each time around the loop.
	$matches = array();
	$attr = '';
	
	// Attempt to parse out the file and line number.
	if (preg_match('/in (.*?) on line ([0-9]+)/', $message, $matches)) {
		$url = 'txmt://open?url=file://' . $matches[1] . '&line=' . $matches[2];
		$attr = ' onclick="window.location=\'' . $url . '\';"';
	}
	
	$view->set('message', $message);
	$view->set('attr', $attr);
	$view->render();
}