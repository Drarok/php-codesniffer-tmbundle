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
	
	$view->set('message', $message);
	$view->render();
}