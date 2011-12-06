<?php
/**
 * A wrapper around php's lint command for use in TextMate.
 *
 * @category  TextMate_Bundles
 * @package   PHPCS_Bundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPLintHelper - run and parse the output from php's lint command.
 *
 * @category TextMate_Bundles
 * @package  PHPCS_Bundle
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class PHPLintHelper extends HelperAbstract
{
	/**
	 * Binary name to search for in getBinaryPath().
	 *
	 * @var string $_binaryName
	 */
	protected $_binaryName = 'php';
	
	/**
	 * Filename to lint check.
	 *
	 * @var $_filename
	 */
	protected $_filename;
	
	/**
	 * Results from executing php's lint command.
	 *
	 * @var array $_results
	 */
	protected $_results;
	
	/**
	 * Constructor.
	 *
	 * @param string $filename Path to the file to lint-check.
	 */
	public function __construct($filename)
	{
		$this->setFilename($filename);
	}
	
	/**
	 * Filename setter.
	 *
	 * @param string $filename Filename to check when calling validate().
	 *
	 * @return void
	 * @throws Exception When the file doesn't exist.
	 */
	public function setFilename($filename)
	{
		if (! file_exists($filename)) {
			throw new Exception('No such file: ' . $filename);
		}
		
		$this->_filename = $filename;
	}
	
	/**
	 * Results getter.
	 *
	 * @return array The results from executing lint.
	 */
	public function getResults()
	{
		return $this->_results;
	}
	
	/**
	 * Validate the file, and return a boolean indicating success or failure.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function validate()
	{
		// Get the path to the binary.
		$cmd = escapeshellcmd($this->_getBinaryPath());
		
		// Add on the commands.
		$cmd .= ' ' . escapeshellarg('-l');
		$cmd .= ' ' . escapeshellarg($this->_filename);
		
		// Initialise our 'out' variables.
		$output = array();
		$exitCode = null;
		
		// Run the full command.
		exec($cmd . ' 2>&1', $output, $exitCode);
		
		// Store the results for later.
		$this->_results = $output;
		
		// Exit code 0 means all went well.
		return ($exitCode == 0);
	}
}