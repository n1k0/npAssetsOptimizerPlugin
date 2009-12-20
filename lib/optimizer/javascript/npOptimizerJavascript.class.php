<?php
/**
 * npAssetsOptimizerPlugin javascript assets optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerJavascript extends npOptimizerBase
{
  protected 
    $destination = '/js/optimized.js';
  
  /**
   * @see npOptimizerBase
   */
  public function configure(array $configuration = array())
  {
    if (isset($configuration['files']))
    {
      parent::setFiles($configuration['files']);
    }
    
    if (isset($configuration['destination']))
    {
      $this->destination = $configuration['destination'];
    }
  }
  
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return parent::computeAssetFilepath($file, 'js', '/js');
  }
  
  public function getOptimizedFileSystemPath()
  {
    return sprintf('%s/%s', sfConfig::get('sf_web_dir'), $this->destination);
  }
  
  public function getOptimizedFileWebPath()
  {
    return $this->destination;
  }
  
  public function generateTimestampedAssetName()
  {
    return sprintf('%s?%d', $this->getOptimizedFileWebPath(), filemtime($this->getOptimizedFileSystemPath()));
  }
  
  /**
   * Optimizes a javascript file
   *
   */
  public function optimizeFile($file)
  {
    return JSMin::minify(file_get_contents($file));
  }
}