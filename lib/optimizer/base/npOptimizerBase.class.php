<?php
/**
 * npAssetsOptimizerPlugin base assets optimizer. Every optimizer must derive from this 
 * class and implement its abstract methods.
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
abstract class npOptimizerBase
{
  protected
    $assetFiles    = array(),
    $baseAssetsDir = null,
    $driver        = null,
    $driverName    = null,
    $dispatcher    = null,
    $files         = array(),
    $replaceFiles  = false;
  
  /**
   * Public constructor
   *
   * @param  sfEventDispatcher  $dispatcher     An event dispatcher instance
   * @param  array              $configuration  Optimizer configuration
   * @param  string|null        $baseAssetsDir  Base assets directory
   */
  public function __construct(sfEventDispatcher $dispatcher, array $configuration, $baseAssetsDir)
  {
    $this->baseAssetsDir = $baseAssetsDir;
    
    $this->dispatcher = $dispatcher;
    
    $this->configure($configuration);
    
    $this->dispatcher->notify(new sfEvent($this, 'optimizer.post_configure', array(
      'configuration' => $configuration,
    )));
  }
  
  /**
   * Retrieves an asset file path from its symfony name (eg. "main", "main.js", "/css/main.css")
   *
   * @param  string  $file
   */
  abstract public function getAssetFilepath($file);
  
  /**
   * Retrieves the original configured list of asset files to process
   *
   * @return array
   */
  public function getAssetFiles()
  {
    return $this->assetFiles;
  }
  
  /**
   * Retrieves the absolute path to an asset from its symfony name, its associated extension
   * and an optional web path. The asset file must reside on the local filesytem, foreign
   * ones will be ignored (this might change with a dedicated option in the future).
   *
   * @param  string  $asset      The symfony asset file name (eg. "main", "main.js", "/css/main.css")
   * @param  string  $extension  The file extension (eg. "css", "js")
   * @param  string  $webPath    An optional web path for reconstructing the real 
   *                             path (always starting with the "/" character)
   *
   * @return string|null
   */
  public function computeAssetFilepath($asset, $extension, $webPath = '/')
  {
    if (preg_match('/^http[s]?:/i', $asset))
    {
      return null;
    }
    
    $webPath = !preg_match('#^/#', $asset) ? sprintf('%s/', $webPath) : '';
    
    $fileName = preg_match(sprintf('/\.%s$/i', $extension), $asset) ? $asset : sprintf('%s.%s', $asset, $extension);
    
    return sprintf('%s%s%s', $this->baseAssetsDir, $webPath, $fileName);
  }
  
  /**
   * Configures the optimizer
   *
   * @param  array  $configuration
   */
  public function configure(array $configuration = array())
  {
    if (!isset($configuration['driver']))
    {
      throw new sfConfigurationException('No optimization driver name specified');
    }
    
    $this->driverName = $configuration['driver'];
    
    $driverClass = sprintf('npDriver%s', $this->driverName);
    
    if (!class_exists($driverClass, true) || !in_array('npDriverBase', class_parents($driverClass)))
    {
      throw new sfConfigurationException(sprintf('Driver class "%s" does not exist or extends npDriverBase', $driverClass));
    }
    
    $this->driver = new $driverClass(isset($configuration['driverOptions']) ? $configuration['driverOptions'] : array());
  }

  /**
   * Retrieves an optimization driver instance
   *
   * @return npDriverBase
   */
  public function getDriver()
  {
    return $this->driver;
  }
  
  /**
   * Retrieves current optimization driver name
   *
   * @return string
   */
  public function getDriverName()
  {
    return $this->driverName;
  }
  
  /**
   * Retrieves the list of files to process
   *
   * @return array
   */
  public function getFiles()
  {
    return $this->files;
  }
  
  /**
   * Optimizes all files
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
    
    $results = array();

    foreach ($this->files as $file)
    {
      $results[$file] = $this->optimizeFile($file);
    }
    
    return array('statistics' => $results);
  }
  
  /**
   * Optimizes a single file
   *
   * @param  string   $file     The optimized asset file path
   *
   * @return array  Optimization results
   */
  public function optimizeFile($file)
  {
    return $this->getDriver($this->driver)
      ->reset()
      ->processFile($file, $this->replaceFiles)
      ->getResults()
    ;
  }
  
  /**
   * Sets files to process
   *
   * @param  array  $files
   *
   * @throws RuntimeException  if a file doesn't exist or can't be resolved
   */
  public function setFiles(array $files = array())
  {
    $this->assetFiles = $files;
    
    foreach ($files as $i => $file)
    {
      if (!file_exists($file) && !file_exists($files[$i] = $this->getAssetFilepath($file)))
      {
        throw new RuntimeException(sprintf('File "%s" does not exist or cannot be resolved (tried "%s" as well)', $file, $files[$i]));
      }
    }
    
    $this->files = $files;
  }
}
