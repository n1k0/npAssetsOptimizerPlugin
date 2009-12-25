<?php
/**
 * npAssetsOptimizerPlugin base optimization driver class
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  driver
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
abstract class npDriverBase
{
  protected 
    $options          = array(),
    $optimizedContent = null,
    $optimizedSize    = null,
    $originalSize     = null,
    $processed        = false;
  
  public function __construct(array $options = array())
  {
    $this->options = $options;
  }
  
  abstract public function doProcessFile($file, $replace = false);
  
  public function getOptimizationRatio()
  {
    return 0 !== $this->originalSize ? round($this->optimizedSize * 100 / $this->originalSize, 2) : null;
  }
  
  public function getResults()
  {
    if (!$this->processed)
    {
      throw new LogicException('Optimization has not been processed');
    }
    
    return array(
      'optimizedContent' => $this->optimizedContent,
      'originalSize'     => $this->originalSize,
      'optimizedSize'    => $this->optimizedSize,
      'ratio'            => $this->getOptimizationRatio(),
    );
  }
  
  public function processFile($file, $replace = false)
  {
    $this->originalSize = filesize($file);
    
    $result = $this->doProcessFile($file, $replace);
    
    if ($replace)
    {
      clearstatcache();
      
      $this->optimizedSize = filesize($result);
    }
    else
    {
      $this->optimizedSize = strlen($result);
      
      $this->optimizedContent = $result;
    }
    
    $this->processed = true;
    
    return $this;
  }
  
  protected function replaceFile($file, $content)
  {
    if (!file_put_contents($file, $content))
    {
      throw new RuntimeException(sprintf('Unable to replace file "%s" with optimized contents', $file));
    }
    
    return $file;
  }
  
  /**
   * Resets driver instance
   *
   * @return npDriverBase
   */
  public function reset()
  {
    $this->optimizedContent = null;
    $this->optimizedSize    = null;
    $this->originalSize     = null;
    $this->processed        = false;
    
    return $this;
  }
}
