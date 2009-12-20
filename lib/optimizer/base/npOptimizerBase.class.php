<?php
/**
 * npAssetsOptimizerPlugin base assets optimizer
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
   * and an optional web path
   *
   * @param  string  $asset      The symfony asset file name (ex. "main", "main.js", "/css/main.css")
   * @param  string  $extension  The file extension (ex. "css", "js")
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
   * Retrieves an asset file path from its symfony name
   *
   * @param  string  $file
   */
  abstract public function getAssetFilepath($file);
  
  /**
   * Optimizes files
   *
   * @return  array  Optimized files
   *
   * @throws RuntimeException
   */
  public function optimize()
  {
    $optimized = array();
    
    foreach ($this->files as $i => $file)
    {
      $optimized[] = $this->optimizeFile($file);
    }
    
    if (!file_put_contents($optimizedFile = sprintf('%s%s', sfConfig::get('sf_web_dir'), $this->destination), implode('', $optimized)))
    {
      throw new RuntimeException(sprintf('Unable to write optimized and combined asset file "%s"', $optimizedFile));
    }
    
    return (array) $optimizedFile;
  }
  
  /**
   * Optimizes a file
   *
   * @param  string  $file  Tha asset file path
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
        unset($files[$i]);
      }
    }
    
    $this->files = $files;
  }
}
