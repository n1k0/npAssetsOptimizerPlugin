<?php
/**
 * npAssetsOptimizerPlugin cssmin stylesheet optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npDriverCssmin extends npDriverBase
{
  public function doProcessFile($file, $replace = false)
  {
    $optimizedContent = cssmin::minify(file_get_contents($file));
    
    if ($replace)
    {
      return parent::replaceFile($file, $optimizedContent);
    }
    else
    {
      return $optimizedContent;
    }
  }
}