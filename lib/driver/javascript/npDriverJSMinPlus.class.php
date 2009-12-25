<?php
/**
 * npAssetsOptimizerPlugin JSMinPlus javascript optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npDriverJSMinPlus extends npDriverBase
{
  public function doProcessFile($file, $replace = false)
  {
    $optimizedContent = JSMinPlus::minify(file_get_contents($file));
    
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