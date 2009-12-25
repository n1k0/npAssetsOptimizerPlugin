<?php
/**
 * npAssetsOptimizerPlugin pngout PNG optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 *
 * @see http://www.jonof.id.au/pngout
 */
class npDriverPngout extends npDriverBase
{
  /**
   * Optimizes a PNG image using PNGOut if available on the system
   *
   * @see npDriverBase
   */
  public function doProcessFile($file, $replace = false)
  {
    if (false === $replace)
    {
      throw new LogicException('PNG optimization only support file replacement atm');
    }
    
    exec('which pngout', $output, $return);
    
    if (!count($output) || $return > 0)
    {      
      throw new RuntimeException('The pngout program is not available nor accessible by php on your system');
    }
    
    exec(sprintf('pngout %s 2>/dev/null', escapeshellarg($file)), $output, $return);
    
    return $file;
  }
}