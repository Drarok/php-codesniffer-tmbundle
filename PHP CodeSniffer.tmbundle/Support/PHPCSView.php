<?php
/**
 * Simple View class for rendering text or HTML.
 *
 * @category  TextMate_Bundles
 * @package   PHPCS_Bundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * PHPCSView - render specified file with variable replacement.
 *
 * @category TextMate_Bundles
 * @package  PHPCS_Bundle
 * @author   Mat Gadd <mgadd@names.co.uk>
 */
class PHPCSView
{
	/**
	 * Factory for view objects.
	 *
	 * @param string $filename File name to load.
	 * @param string $ext      File extension to load, defaults to '.php'.
	 *
	 * @return object New instance.
	 */
	public static function factory($filename, $ext = 'php')
	{
		return new PHPCSView($filename, $ext);
	}
	
	/**
	 * View filename to render.
	 *
	 * @var string $_filename
	 */
	protected $_filename;
	
	/**
	 * Variable storage for use when rendering.
	 *
	 * @var array $_variables
	 */
	protected $_variables = array();
	
	/**
	 * Output when rendering (used internally).
	 *
	 * @var bool $_output
	 */
	protected $_output;
	
	/**
	 * Constructor.
	 *
	 * @param string $filename File name to load.
	 * @param string $ext      File extension to load, defaults to '.php'.
	 */
	public function __construct($filename, $ext = 'php')
	{
		$this->setFilename('views/' . $filename . '.' . $ext);
	}
	
	/**
	 * Magic toString method - render and return.
	 *
	 * @return string Rendered view.
	 */
	public function __toString()
	{
		return $this->render(false);
	}
	
	/**
	 * Filename setter.
	 *
	 * @param string $filename Filename to render.
	 *
	 * @return void
	 */
	public function setFilename($filename)
	{
		$this->_filename = $filename;
	}
	
	/**
	 * Variable setter for substitution when rendering.
	 *
	 * @param string|array $key   Name to use during replacement, or array of
	 * key => value pairs.
	 * @param mixed        $value Variable content to use as replacement.
	 *
	 * @return object Current instance (allows chaining).
	 */
	public function set($key, $value = null)
	{
		if (is_array($key) && is_null($value)) {
			foreach ($key as $k => $v) {
				$this->set($k, $v);
			}
		} else {
			$this->_variables[$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Render the view file with variable substitution.
	 *
	 * @param bool $output Pass true to immediately output, false to buffer and
	 * return.
	 *
	 * @return string Rendered output.
	 */
	public function render($output = true)
	{
		$this->_output = $output;
		return $this->_render();
	}
	
	/**
	 * Actual rendering method, using no in-scope variables.
	 *
	 * @return string Rendered output.
	 */
	protected function _render()
	{
		extract($this->_variables, EXTR_OVERWRITE);
		
		if (! $this->_output) {
			ob_start();
		}
		
		include $this->_filename;
		
		if (! $this->_output) {
			return ob_get_clean();
		}
	}
}