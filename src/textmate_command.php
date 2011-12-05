#!/usr/bin/php
<?php
/**
 * TextMate PHP CodeSniffer command.
 *
 * @category  TextMate Bundles
 * @package   PHPCS TMBundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

// Make sure we have a file path to work on.
if (isset($_SERVER['TM_FILEPATH'])) {
	$fileName = $_SERVER['TM_FILEPATH'];
} else {
	throw new Exception('No file path specified.');
}

if (isset($_SERVER['TM_BUNDLE_PATH'])) {
	$bundlePath = $_SERVER['TM_BUNDLE_PATH'];
} else {
	throw new Exception('Failed to detect Bundle path.');
}

// Update the include path to add our support classes.
set_include_path(
	$bundlePath
	. PATH_SEPARATOR
	. get_include_path());

// Require the files we need.
require_once 'PHPCSHelper.php';
require_once 'PHPCSView.php';

// Create a codesniffer wrapper, set standard and validate.
$cs = new PHPCSHelper($fileName);
$cs->setStandard('Namesco');
$valid = $cs->validate();

// Create supporting view objects.
$script = PHPCSView::factory('results', 'js')->set('cs', $cs);
$style = PHPCSView::factory('style_screen', 'css');

// Create the main content view.
$content = PHPCSView::factory('content')
	->set('cs', $cs)
	->set('valid', $valid);

// Create and set up the main template.
$template = PHPCSView::factory('results')
	->set('script', $script)
	->set('style', $style)
	->set('content', $content);

// Output the results!
$template->render();