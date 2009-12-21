<?php
/**
 * npAssetsOptimizerPlugin base assets optimizer. Every optimizer must derive from this 
 * class and implement its abstract methods.
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
abstract class npOptimizerBase
{
  protected 
    $dispatcher = null,
    $files      = array();
  
  /**
   * Public constructor
   *
   * @param  sfEventDispatcher  $dispatcher     An event dispatcher instance
   * @param  array              $configuration  Optimizer configuration
   */
  public function __construct(sfEventDispatcher $dispatcher, array $configuration = array())
  {
    $this->dispatcher = $dispatcher;
    
    $this->configure($configuration);
  }
  
  /**
   * Retrieves the absolute path to an asset from its symfony name, its associated extension
   * and an optional web path. The asset file must reside on the local filesytem, foreign
   * ones will be ignored (this might change with a dedicated option in the future).
   *
   * @param  string  $asset      The symfony asset file name (eg. "main", "main.js", "/css/main.css")
   * @param  string  $extension  The file extension (eg. "css", "js")
   * @param  string  $webPath    An optional web path for reconstructing the real 
   *                             path (always starting with the "/" character)
   *
   * @return string|null
   */
  public function computeAssetFilepath($asset, $extension, $webPath = '/')
  {
    if (preg_match('/^http[s]?:/i', $asset))
    {
      return null;
    }
    
    $webPath = !preg_match('#^/#', $asset) ? sprintf('%s/', $webPath) : '';
    
    $fileName = preg_match(sprintf('/\.%s$/i', $extension), $asset) ? $asset : sprintf('%s.%s', $asset, $extension);
    
    return sprintf('%s%s%s', sfConfig::get('sf_web_dir'), $webPath, $fileName);
  }
  
  /**
   * Configures the optimizer
   *
   * @param  array  $configuration
   */
  abstract public function configure(array $configuration = array());

  /**
   * Retrieves an asset file path from its symfony name (eg. "main", "main.js", "/css/main.css")
   *
   * @param  string  $file
   */
  abstract public function getAssetFilepath($file);
  
  /**
   * Optimize assets
   *
   * @return array  The list of optimized files
   */
  abstract public function optimize();
  
  /**
   * Optimizes a single file
   *
   * @param  string  $file  The optimized asset file path
   */
  abstract public function optimizeFile($file);
  
  /**
   * Sets files to process
   *
   * @param  array  $files
   */
  public function setFiles(array $files = array())
  {
    foreach ($files as $i => $file)
    {
      if (!file_exists($file) && !file_exists($files[$i] = $this->getAssetFilepath($file)))
      {
        unset($files[$i]); // silently removes non-existent files
      }
    }
    
    $this->files = $files;
  }
}
