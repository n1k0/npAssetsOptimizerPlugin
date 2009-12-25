<?php
/**
 * npAssetsOptimizerPlugin Minify CSS Compressor stylesheet optimization driver
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 *
 * @see http://minify.googlecode.com/svn/trunk/min/lib/Minify/CSS/Compressor.php
 */
class npDriverMinifyCssCompressor extends npDriverBase
{
  public function doProcessFile($file, $replace = false)
  {
    $optimizedContent = Minify_CSS_Compressor::process(file_get_contents($file));
    
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