<?php
/**
 * npAssetsOptimizerPlugin base combinable assets optimizer. Every combinable optimizer must
 * derive from this class and implement its abstract methods.
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
abstract class npOptimizerCombinableBase extends npOptimizerBase
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
    
    if (!isset($configuration['destination']))
    {
      throw new sfConfigurationException('You must provide a "destination" option to use a combinable optimizer');
    }
    
    $this->destination = $configuration['destination'];
  }
  
  /**
   * Computes and returns the absolute path to the optimized file
   *
   * @return string
   */
  public function getOptimizedFileSystemPath()
  {
    return sprintf('%s/%s', sfConfig::get('sf_web_dir'), $this->destination);
  }
  
  /**
   * Computes and returns the web path to the optimized file
   *
   * @return string
   */
  public function getOptimizedFileWebPath()
  {
    return $this->destination;
  }
  
  /**
   * Computes and returns the web path to the optimized file with the creation timestamp as
   * a GET parameter (if the files actually exists on the filesystem)
   *
   * @return string
   */
  public function generateTimestampedAssetName()
  {
    if (file_exists($filePath = $this->getOptimizedFileSystemPath()))
    {
      return sprintf('%s?%d', $this->getOptimizedFileWebPath(), filemtime($filePath));
    }
    
    return $this->getOptimizedFileWebPath();
  }
  
  /**
   * Combines the results of every file optimization in one single file.
   *
   * @return  array  The list of resulting optimized files
   *
   * @throws RuntimeException if a problem occurs
   */
  public function optimize()
  {
    if (!count($this->files))
    {
      throw new RuntimeException(sprintf('No files to optimize'));
    }
    
    $optimized = array();
    
    foreach ($this->files as $file)
    {
      $optimized[] = $this->optimizeFile($file);
    }
    
    $optimizedContents = implode('', $optimized);
    
    if (empty($optimizedContents))
    {
      throw new RuntimeException('Empty optimized contents!');
    }
    if (!file_put_contents($optimizedFile = sprintf('%s%s', sfConfig::get('sf_web_dir'), $this->destination), $optimizedContents))
    {
      throw new RuntimeException(sprintf('Unable to write optimized and combined asset file "%s"', $optimizedFile));
    }
    
    return (array) $optimizedFile; // an array containing the path to the combined file
  }
}