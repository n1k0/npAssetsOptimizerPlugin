<?php
/**
 * npAssetsOptimizerPlugin pngcrush PNG optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npDriverPngCrush extends npDriverBase
{
  /**
   * Optimizes a PNG image using pngcrush if available on the system
   *
   * @see npDriverBase
   */
  public function doProcessFile($file, $replace = false)
  {
    if (false === $replace)
    {
      throw new LogicException('PNG optimization only support file replacement atm');
    }
    
    exec('which pngcrush', $output, $return);
    
    if (!count($output) || $return > 0)
    {      
      throw new RuntimeException('The pngcrush program is not available nor accessible by php');
    }
    
    $tmpFile = sprintf('%s.tmp', $file);
    
    exec(sprintf('pngcrush %s %s 2>/dev/null', escapeshellarg($file), escapeshellarg($tmpFile)), $output, $return);
    
    if (file_exists($tmpFile) && filesize($tmpFile) > filesize($file))
    {
      copy($tmpFile, $file);
    }
    
    unlink($tmpFile);
    
    return $file;
  }
}