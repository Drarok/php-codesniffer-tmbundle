<?php
/**
 * TextMate PHP CodeSniffer command.
 *
 * @category  TextMate_Bundles
 * @package   PHPCS_Bundle
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
	$bundleSupport = $_SERVER['TM_BUNDLE_PATH']
		. DIRECTORY_SEPARATOR . 'Support';
} elseif (isset($_SERVER['TM_BUNDLE_SUPPORT'])) {
	$bundleSupport = $_SERVER['TM_BUNDLE_SUPPORT'];
} else {
	throw new Exception('Failed to detect Bundle path.');
}

// Update the include path to add our support classes.
set_include_path($bundleSupport . PATH_SEPARATOR . get_include_path());

// Require the files we need.
require_once 'HelperAbstract.php';
require_once 'PHPCSHelper.php';
require_once 'PHPLintHelper.php';
require_once 'PHPCSView.php';

// Create a codesniffer wrapper, set standard and validate.
$cs = new PHPCSHelper($fileName);

// Allow users to set an environment variable to specify the standard.
if (isset($_SERVER['PHPCS_STANDARD'])) {
	$cs->setStandard($_SERVER['PHPCS_STANDARD']);
}

// Run the CodeSniffer validation.
$csValid = $cs->validate();

// Create supporting view objects.
$script = PHPCSView::factory('results', 'js')->set('cs', $cs);

// Create the CodeSniffer content view.
$phpcsContent = PHPCSView::factory('phpcs_content')
	->set('cs', $cs)
	->set('valid', $csValid);

// Create the Lint content view.
$lint = new PHPLintHelper($fileName);
$lintContent = PHPCSView::factory('phplint_content')
	->set('lint', $lint)
	->set('valid', $lint->validate());

// Create and set up the wrapping template.
$template = PHPCSView::factory('template')
	->set('cs', $cs)
	->set('script', $script)
	->set('style', PHPCSView::factory('style_screen', 'css'))
	->set('phpcsContent', $phpcsContent)
	->set('lintContent', $lintContent);

// Output the results!
$template->render();