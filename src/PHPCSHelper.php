<?php
/**
 * A wrapper around phpcs for use in TextMate.
 *
 * @category  TextMate Bundles
 * @package   PHPCS TMBundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPCSHelper - run and parse the output from phpcs.
 *
 * @category TextMate Bundles
 * @package  PHPCS TMBundle
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class PHPCSHelper
{
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
		$cmd = 'phpcs';
		
		$args = array(
			'--report=xml',
		);
		
		if ($this->_standard) {
			$args[] = '--standard=' . $this->_standard;
		}
		
		// Build the full command.
		$exec = escapeshellcmd($cmd);
		
		foreach ($args as $arg) {
			$exec .= ' ' . escapeshellarg($arg);
		}
		
		$exec .= ' ' . escapeshellarg($this->_filename);
		
		$output = array();
		$exitCode = null;
		exec($exec, $output, $exitCode);
		
		if ($exitCode == 0) {
			return true;
		}
		
		$this->_errorCount = 0;
		$this->_warningCount = 0;
		$this->_violations = array();
		
		$xml = simplexml_load_string(implode(PHP_EOL, $output));
		
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
		$type = $ele->getName();
		$line = $ele['line'];
		$col = $ele['column'];
		$sev = $ele['severity'];
		$txmt = sprintf('txmt://open?url=file://%s&line=%d&column=%d',
			$filename, $line, $col);
		
		ob_start();
		echo
			'<div id="', $id, '" class="', $type, '" txmt="', $txmt, '">',
			PHP_EOL
		;
		echo "\t", '<span class="type">', ucfirst($type), '</span>', PHP_EOL;
		echo "\t", '<span class="line">(line ', $line, ')</span>', PHP_EOL;
		echo
			"\t", '<div class="error-msg">', htmlentities((string) $ele),
			'</div>', PHP_EOL
		;
		echo '</div>', PHP_EOL;
		
		return ob_get_clean();
	}
}