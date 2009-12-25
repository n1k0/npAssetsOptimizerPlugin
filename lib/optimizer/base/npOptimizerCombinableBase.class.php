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
  protected 
    $destination = null,
    $timestamp   = false;
  
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
    
    if (!isset($configuration['destination']))
    {
      throw new sfConfigurationException('You must provide a "destination" option to use a combinable optimizer');
    }
    
    $this->destination = $configuration['destination'];
    
    if (isset($configuration['timestamp']))
    {
      $this->timestamp = $configuration['timestamp'];
    }
    
    $this->replaceFiles = false;
  }
  
  /**
   * Computes and returns the absolute path to the optimized file
   *
   * @return string
   */
  public function getOptimizedFileSystemPath()
  {
    return realpath(sprintf('%s/%s', $this->baseAssetsDir, $this->destination));
  }
  
  /**
   * Computes and returns the web path to the optimized file
   *
   * @return string
   */
  public function getOptimizedFileWebPath()
  {
    if (true === $this->timestamp)
    {
      return $this->generateTimestampedAssetName();
    }
    else
    {
      return $this->destination;
    }
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
      return sprintf('%s?%d', $this->destination, filemtime($filePath));
    }
    
    return $this->destination;
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
    $results = parent::optimize();
    
    $optimizedContents = '';
    
    foreach ($results['statistics'] as $file => $statistic)
    {
      $optimizedContents .= $statistic['optimizedContent'];
    }
    
    if (empty($optimizedContents))
    {
      throw new RuntimeException('Empty optimized contents!');
    }
    
    if (!file_put_contents($optimizedFile = sprintf('%s%s', $this->baseAssetsDir, $this->destination), $optimizedContents))
    {
      throw new RuntimeException(sprintf('Unable to write optimized and combined asset file "%s"', $optimizedFile));
    }
    
    return array_merge($results, array('generatedFile' => $optimizedFile));
  }
}
