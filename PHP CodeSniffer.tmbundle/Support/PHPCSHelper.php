<?php
/**
 * A wrapper around phpcs for use in TextMate.
 *
 * @category  TextMate_Bundles
 * @package   PHPCS_Bundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPCSHelper - run and parse the output from phpcs.
 *
 * @category TextMate_Bundles
 * @package  PHPCS_Bundle
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class PHPCSHelper extends HelperAbstract
{
	/**
	 * Binary name to search for in getBinaryPath().
	 *
	 * @var $_binaryName
	 */
	protected $_binaryName = 'phpcs';
	
	/**
	 * Filename to parse.
	 *
	 * @var $_filename string
	 */
	protected $_filename;
	
	/**
	 * PHPCodeSniffer standard to use when parsing.
	 *
	 * @var $_standard string
	 */
	protected $_standard;
	
	/**
	 * Counter for errors parsed from phpcs.
	 *
	 * @var int $_errorCount
	 */
	protected $_errorCount = 0;
	
	/**
	 * Counter for warnings parsed from phpcs.
	 *
	 * @var int $_warningCount
	 */
	protected $_warningCount = 0;
	
	/**
	 * Structured data, parsed from phpcs.
	 *
	 * @var array $_violations
	 */
	protected $_violations = array();
	
	/**
	 * Constructor.
	 *
	 * @param string $filename Filename to parse.
	 * @param mixed  $standard Standard to use when parsing.
	 */
	public function __construct($filename, $standard = null)
	{
		$this->setFilename($filename);
		$this->setStandard($standard);
	}
	
	/**
	 * Filename setter.
	 *
	 * @param string $filename Filename to parse.
	 *
	 * @return void
	 */
	public function setFilename($filename)
	{
		$this->_filename = $filename;
	}
	
	/**
	 * Standard setter.
	 *
	 * @param string $standard Standard to use when parsing.
	 *
	 * @return void
	 */
	public function setStandard($standard)
	{
		$this->_standard = $standard;
	}
	
	/**
	 * Coding standard getter.
	 *
	 * @return string Current coding standard.
	 */
	public function getStandard()
	{
		return $this->_standard;
	}
	
	/**
	 * Error count getter.
	 *
	 * @return int Error count from parsed phpcs output.
	 */
	public function getErrorCount()
	{
		return $this->_errorCount;
	}
	
	/**
	 * Warning count getter.
	 *
	 * @return int Warning count from parsed phpcs output.
	 */
	public function getWarningCount()
	{
		return $this->_warningCount;
	}
	
	/**
	 * Violations getter.
	 *
	 * @return array Parsed violations.
	 */
	public function getViolations()
	{
		return $this->_violations;
	}
	
	/**
	 * Main validation function - does all the heavy lifting.
	 *
	 * @return bool
	 */
	public function validate()
	{
		// Initialise the result vars.
		$this->_errorCount = 0;
		$this->_warningCount = 0;
		$this->_violations = array();
		
		// Tell phpcs to give us XML.
		$args = array(
			'-d',
			'error_reporting=0',
			'--report=xml',
		);
		
		if ($this->_standard) {
			// Add the the coding standard, if set.
			$args[] = '--standard=' . $this->_standard;
		}
		
		// Start building the full command.
		$exec = escapeshellcmd($this->_getBinaryPath());
		
		// Add on each argument.
		foreach ($args as $arg) {
			$exec .= ' ' . escapeshellarg($arg);
		}
		
		// End with the file name.
		$exec .= ' ' . escapeshellarg($this->_filename);
		
		// Initialise the vars and run the command.
		$output = array();
		$exitCode = null;
		exec($exec, $output, $exitCode);
		
		if ($exitCode == 0) {
			// Exit code 0 signifies everything's ok.
			return true;
		}
		
		// Join together each element from the result array.
		$resultString = implode(PHP_EOL, $output);
		
		if (strtoupper(substr($resultString, 0, 5)) == 'ERROR') {
			// If the string began with 'ERROR', throw it.
			throw new Exception($resultString);
		}
		
		// Attempt to load the result as XML.
		$xml = simplexml_load_string($resultString);
		
		// Loop over each file result, adding each standard violation.
		foreach ($xml->file as $file) {
			$filename = $file['name'];
			
			foreach ($file->children() as $violation) {
				$type = $violation->getName();
				
				if ($type == 'error') {
					$count = ++$this->_errorCount;
				} else {
					$count = ++$this->_warningCount;
				}
				
				$id = $type[0] . $count;
				
				$this->_violations[] = self::_renderViolation(
					$filename, $violation, $id);
			}
		}
		
		return false;
	}
	
	/**
	 * Render a single violation as HTML.
	 *
	 * @param string $filename Filename the violation  occurred in.
	 * @param object $ele      XML element to render.
	 * @param string $id       Unique identifier for this violation.
	 *
	 * @return string Rendered violation HTML.
	 */
	protected static function _renderViolation($filename, $ele, $id)
	{
		static $view = null;
		
		if (! $view) {
			$view = PHPCSView::factory('violation');
		}
		
		$view->set(array(
			'id' => $id,
			'ele' => $ele,
			'type' => $ele->getName(),
			'line' => $line = $ele['line'],
			'col' => $col = $ele['col'],
			'txmt' => sprintf(
				'txmt://open?url=file://%s&line=%d&column=%d',
				$filename, $line, $col)
		));
		
		return $view->render(false);
	}
}