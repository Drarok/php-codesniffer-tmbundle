<?php
/**
 * A base class for helpers.
 *
 * @category  TextMate_Bundles
 * @package   PHPCS_Bundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * HelperAbstract - base functionality for helpers.
 *
 * @category TextMate_Bundles
 * @package  PHPCS_Bundle
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
abstract class HelperAbstract
{
	/**
	 * Binary name to search for in getBinaryPath().
	 *
	 * @var string $_binaryName
	 */
	protected $_binaryName = null;
	
	/**
	 * Cached path to the binary.
	 *
	 * @var string $_binaryPath
	 */
	protected $_binaryPath = null;
	
	
	/**
	 * Find the phpcs binary on the environment path.
	 *
	 * @return string Path to the binary.
	 * @throws Exception If finding the binary fails.
	 */
	protected function _getBinaryPath()
	{
		if (! $this->_binaryPath) {
			$paths = explode(PATH_SEPARATOR, $_SERVER['PATH']);
			foreach ($paths as $path) {
				$path .= DIRECTORY_SEPARATOR . $this->_binaryName;
				if (file_exists($path)) {
					$this->_binaryPath = $path;
					break;
				}
			}
			
			if (! $this->_binaryPath) {
				throw new Exception('Failed to find phpcs binary.');
			}
		}
		
		return $this->_binaryPath;
	}

}