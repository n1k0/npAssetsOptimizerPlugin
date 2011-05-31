<?php
/**
 * npAssetsOptimizerPlugin jpegtran JPEG optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Clément Herreman <clement.herreman@gmail.com>
 *
 * @see http://jpegclub.org/jpegtran/
 */
class npDriverJpegtran extends npDriverBase
{
  /**
   * Optimizes a JPEG image using Jpegtran if available on the system
   *
   * @see npDriverBase
   */
  public function doProcessFile($file, $replace = false)
  {
    if (false === $replace)
    {
      throw new LogicException('JPEG optimization only support file replacement atm');
    }
    
    exec('which jpegtran', $output, $return);
    
    if (!count($output) || $return > 0)
    {      
      throw new RuntimeException('The jpegtran program is not available nor accessible by php on your system');
    }
    
    exec(sprintf('jpegtran -copy none -optimize %s 2>/dev/null', escapeshellarg($file)), $output, $return);

    $this->replaceFile($file, $output);
    
    return $file;
  }
}
