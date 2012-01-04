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

    $tmpFile = sprintf('%s.tmp', $file);

    exec(sprintf('jpegtran -copy none -optimize -perfect %s > %s', escapeshellarg($file), escapeshellarg($tmpFile)), $output, $return);

    if (file_exists($tmpFile) && filesize($tmpFile) < filesize($file))
    {
      copy($tmpFile, $file);
    }

    unlink($tmpFile);

    return $file;
  }
}
