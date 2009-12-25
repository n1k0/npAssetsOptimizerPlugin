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
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return parent::computeAssetFilepath($file, 'js', '/js');
  }
}
