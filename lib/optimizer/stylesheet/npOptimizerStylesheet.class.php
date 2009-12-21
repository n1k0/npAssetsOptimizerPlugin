<?php
/**
 * npAssetsOptimizerPlugin stylesheet optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerStylesheet extends npOptimizerCombinableBase
{
  protected 
    $destination = '/css/optimized.css';
  
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return parent::computeAssetFilepath($file, 'css', '/css');
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