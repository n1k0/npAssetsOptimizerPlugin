<?php
/**
 * npAssetsOptimizerPlugin stylesheet optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerStylesheet extends npOptimizerBase
{
  protected 
    $destination = '/css/optimized.css';
  
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
    return parent::computeAssetFilepath($file, 'css', '/css');
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
   * Optimizes a stylesheet file
   *
   * @see npOptimizerBase
   */
  public function optimizeFile($file)
  {
    return cssmin::minify(file_get_contents($file));
  }
}