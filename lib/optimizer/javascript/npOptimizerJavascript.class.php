<?php
/**
 * npAssetsOptimizerPlugin javascript assets optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerJavascript extends npOptimizerCombinableBase
{
  static protected $availableDrivers = array('jsmin', 'google_closure_compiler');
  
  protected 
    $destination = '/js/optimized.js';

  /**
   * Configures this optimizer and checks for a "driver" configuration option
   *
   * @see npOptimizerCombinableBase
   */
  public function configure(array $configuration = array())
  {
    parent::configure($configuration);
    
    $this->driver = isset($configuration['driver']) && !is_null($configuration['driver']) ? $configuration['driver'] : 'jsmin';
    
    if (!in_array($this->driver, self::$availableDrivers))
    {
      throw new sfConfigurationException(sprintf('Javascript optimizer driver "%s" is not available', $this->driver));
    }
  }
  
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return parent::computeAssetFilepath($file, 'js', '/js');
  }
  
  /**
   * Get driver name
   *
   * @return string
   */
  public function getDriver()
  {
    return $this->driver;
  }
  
  /**
   * Returns optimized contents for a javascript file
   *
   * @param  string  $file  The path to javascript file
   *
   * @return string
   */
  public function optimizeFile($file)
  {
    switch ($this->driver)
    {
      case 'google_closure_compiler':
        return $this->compileWithGoogleClosure(file_get_contents($file));
        break;
      
      case 'jsmin':
      default:
        return JSMin::minify(file_get_contents($file));
        break;
    }
  }
  
  /**
   * Returns javascript contents compressed using the Google Closure API
   *
   * @param  string  $script  The original javascript contents
   *
   * @return string
   *
   * @see http://code.google.com/intl/en_US/closure/compiler/docs/api-ref.html
   */
  public function compileWithGoogleClosure($script)
  {
    $ch = curl_init('http://closure-compiler.appspot.com/compile');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code='.urlencode($script));
    $output = curl_exec($ch);
    curl_close($ch);
    
    return $output;
  }
}
