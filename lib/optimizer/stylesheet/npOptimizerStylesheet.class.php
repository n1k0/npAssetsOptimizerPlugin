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
   * @param  string  $file  The path to stylesheet file
   *
   * @return array  Optimization results
   */
  public function optimizeFile($file)
  {
    return parent::getDriver($this->driver)
      ->reset()
      ->processFile($file, false)
      ->getResults()
    ;
  }
}