<?php
/**
 * npAssetsOptimizerPlugin PNG images optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerPngImage extends npOptimizerBase
{
  /**
   * @see npOptimizerBase
   */
  public function configure(array $configuration = array())
  {
    parent::configure($configuration);
    
    if (isset($configuration['files']))
    {
      parent::setFiles($configuration['files']);
    }
    else if (isset($configuration['folders']))
    {
      parent::setFiles($this->findPngImages($configuration['folders']));
    }
    else
    {
      throw new sfConfigurationException('You must define either a "files" or a "folders" option to use this optimizer');
    }
    
    // PNG images will be replaced by their optimized versions
    $this->replaceFiles = true;
  }
  
  /**
   * Finds PNG images within provided absolute paths
   *
   * @param  array  $folders  An array of absoulte forlder paths
   *
   * @return array of files absolutes paths to PNG images
   */
  public function findPngImages(array $folders = array())
  {
    $files = array();

    foreach ($folders as $folder)
    {
      if (!is_dir($folder))
      {
        throw new InvalidArgumentException(sprintf('"%s" is not a valid readable directory'));
      }
      
      foreach (sfFinder::type('file')->name('*.png')->in($folder) as $file)
      {
        $files[] = $file;
      }
    }
    
    return $files;
  }
  
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return $file;
  }
}
