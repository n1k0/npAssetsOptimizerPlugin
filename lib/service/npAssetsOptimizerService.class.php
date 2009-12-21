<?php
/**
 * npAssetsOptimizerPlugin assets optimizer service (factory)
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  service
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npAssetsOptimizerService
{
  protected
    $configuration = array(
      'png_image' => array(
        'enabled' => false,
        'class' => 'nbOptimizerPngImage',
        'params' => array(
          'driver' => null,
        ),
      ),
      'javascript' => array(
        'enabled' => false,
        'class' => 'npOptimizerJavascript',
        'params' => array(
          'driver' => null,
          'files' => array(),
        ),
      ),
      'stylesheet' => array(
        'enabled' => false,
        'class' => 'npOptimizerStylesheet',
        'params' => array(
          'driver' => null,
          'files' => array(),
        ),
      ),
    ),
    $dispatcher = null;
  
  /**
   * Public constructor
   *
   * @param  sfEventDispatcher  $dispatcher
   * @param  array              $configuration
   */
  public function __construct(sfEventDispatcher $dispatcher, array $configuration = array())
  {
    $this->dispatcher = $dispatcher;
    
    $this->configuration = array_merge($this->configuration, $configuration);
  }
  
  /**
   * Optimize javascripts
   *
   * @return array Optimized javascripts
   */
  public function optimizeJavascripts()
  {
    if (true !== $this->configuration['javascript']['enabled'])
    {
      return array();
    }
    
    return $this->getOptimizer('javascript')->optimize();
  }
  
  /**
   * Optimize PNG images
   *
   * @return array Optimized images
   */
  public function optimizePngImages()
  {
    if (true !== $this->configuration['png_image']['enabled'])
    {
      return array();
    }
    
    return $this->getOptimizer('png_image')->optimize();
  }
  
  /**
   * Optimize stylesheets
   *
   * @return array Optimized stylesheets
   */
  public function optimizeStylesheets()
  {
    if (true !== $this->configuration['stylesheet']['enabled'])
    {
      return array();
    }
    
    return $this->getOptimizer('stylesheet')->optimize();
  }
  
  /**
   * Replaces response original javascripts by optimized ones
   *
   * @param  sfWebresponse  $response
   */
  public function replaceJavascripts(sfWebResponse $response)
  {
    if (!$this->configuration['javascript']['enabled'])
	  {
	    return;
    }

    foreach ($this->configuration['javascript']['params']['files'] as $file)
    {
      $response->removeJavascript($file);
    }
    
    $response->addJavascript($this->getOptimizer('javascript')->generateTimestampedAssetName(), 'first');
  }
  
  /**
   * Replaces response original stylesheets by optimized ones
   *
   * @param  sfWebresponse  $response
   */
  public function replaceStylesheets(sfWebResponse $response)
  {
    if (!$this->configuration['stylesheet']['enabled'])
	  {
	    return;
    }

    foreach ($this->configuration['stylesheet']['params']['files'] as $file)
    {
      $response->removeStylesheet($file);
    }
    
    $response->addStylesheet($this->getOptimizer('stylesheet')->generateTimestampedAssetName(), 'first');
  }
  
  /**
   * Creates an optimizer instance from a given configuration array
   *
   * @param  string  $type  The optimizer type
   *
   * @return npOptimizerBase
   *
   * @throws sfConfigurationException
   */
  public function getOptimizer($type)
  {
    if (!in_array($type, $supported = array_keys($this->configuration)))
    {
      throw new sfConfigurationException(sprintf('Optimizer type "%s" is not supported. Available and supported types are %s', $type, implode(', ', $supported)));
    }
    
    $className = $this->configuration[$type]['class'];
    
    if (!class_exists($className, true) || !in_array('npOptimizerBase', class_parents($className)))
    {
      throw new sfConfigurationException(sprintf('Optimizer class "%s" does not exist nor extends the npOptimizerBase class. Please checkout the documentation.', $className));
    }
    
    return new $className($this->dispatcher, $this->configuration[$type]['params']);
  }
}