<?php
/**
 * npAssetsOptimizerPlugin pngout PNG optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npDriverAdvPNG extends npDriverBase
{
  /**
   * Optimizes a PNG image
   *
   * @see npDriverBase
   */
  public function doProcessFile($file, $replace = false)
  {
    if (false === $replace)
    {
      throw new LogicException('PNG optimization only support file replacement atm');
    }
    
    exec('which advpng', $output, $return);
    
    if (!count($output) || $return > 0)
    {      
      throw new RuntimeException('The advpng program is not available nor accessible by php on your system');
    }
    
    exec(sprintf('advpng -4 -z %s 2>/dev/null', escapeshellarg($file)), $output, $return);
    
    return $file;
  }  
}