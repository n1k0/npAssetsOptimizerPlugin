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
      throw new InvalidArgumentException('You must define either a "files" or a "folder" option to use this optimizer');
    }
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
   * Replaces PNG images by their enhanced versions.
   *
   * @see npOptimizerBase
   */
  public function optimize()
  {
    $optimized = array();
    
    foreach ($this->files as $file)
    {
      if (!file_exists($file))
      {
        throw new RuntimeException(sprintf('File %s does not exist', $file));
      }
      
      if (null !== $this->optimizeFile($file))
      {
        $optimized[] = $optimizedFile;
      }
    }
    
    return $optimized;
  }
  
  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return $file; // TODO
  }
  
  /**
   * Optimizes a PNG image using PNGOut if available on the system
   *
   * @see http://www.jonof.id.au/pngout
   */
  public function optimizeFile($file)
  {
    exec('which pngout', $output, $return);
    
    if (!count($output) || $return > 0)
    {      
      throw new RuntimeException('The pngout program is not available nor accessible by php');
    }
    
    exec(sprintf('pngout %s 2>/dev/null', escapeshellarg($file)), $output, $return);
    
    if (!count($output) || $return > 0)
    {
      return null; // file has not been optimized
    }
    
    return $file;
  }
}
