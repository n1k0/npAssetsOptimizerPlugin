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
    $baseAssetsDir = null,
    $configuration = array(
      'png_image' => array(
        'enabled' => false,
        'class' => 'npOptimizerPngImage',
        'params' => array(
          'driver' => 'Pngout',
        ),
      ),
      'javascript' => array(
        'enabled' => false,
        'class' => 'npOptimizerJavascript',
        'params' => array(
          'driver' => 'JSMin',
          'files' => array(),
          'destination' => '/js/optimized.js',
        ),
      ),
      'stylesheet' => array(
        'enabled' => false,
        'class' => 'npOptimizerStylesheet',
        'params' => array(
          'driver' => 'Cssmin',
          'files' => array(),
          'destination' => '/css/optimized.css',
        ),
      ),
    ),
    $dispatcher    = null;
  
  /**
   * Public constructor
   *
   * @param  sfEventDispatcher  $dispatcher
   * @param  array              $configuration
   * @param  string|null        $baseAssetsDir  Base assets directory
   */
  public function __construct(sfEventDispatcher $dispatcher, array $configuration = array(), $baseAssetsDir = null)
  {
    $this->dispatcher = $dispatcher;
    
    $this->configuration = sfToolkit::arrayDeepMerge($this->configuration, $configuration);
    
    $this->baseAssetsDir = null !== $baseAssetsDir ? $baseAssetsDir : sfConfig::get('sf_web_dir');
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
   * Replaces response original javascripts by optimized ones (only if javascript 
   * optimization has been enabled by configuration)
   *
   * @param  sfWebresponse  $response
   */
  public function replaceJavascripts(sfWebResponse $response)
  {
    if (is_null($javascriptOptimizer = $this->getOptimizer('javascript')))
	  {
	    return;
    }

    foreach ($javascriptOptimizer->getAssetFiles() as $file)
    {
      $response->removeJavascript($file);
    }
    
    $response->addJavascript($javascriptOptimizer->getOptimizedFileWebPath(), 'first');
  }
  
  /**
   * Replaces response original stylesheets by optimized ones (only if stylesheet 
   * optimization has been enabled by configuration)
   *
   * @param  sfWebresponse  $response
   */
  public function replaceStylesheets(sfWebResponse $response)
  {
    if (is_null($stylesheetOptimizer = $this->getOptimizer('stylesheet')))
	  {
	    return;
    }

    foreach ($stylesheetOptimizer->getAssetFiles() as $file)
    {
      $response->removeStylesheet($file);
    }
    
    $response->addStylesheet($stylesheetOptimizer->getOptimizedFileWebPath(), 'first');
  }
  
  /**
   * Creates an optimizer instance from a given configuration array
   *
   * @param  string  $type  The optimizer type
   *
   * @return npOptimizerBase|null
   *
   * @throws sfConfigurationException
   */
  public function getOptimizer($type)
  {
    if (!in_array($type, $supported = array_keys($this->configuration)))
    {
      throw new sfConfigurationException(sprintf('Optimizer type "%s" is not supported. Available and supported types are %s', $type, implode(', ', $supported)));
    }
    
    if (false === $this->configuration[$type]['enabled'])
    {
      return null;
    }
    
    $className = $this->configuration[$type]['class'];
    
    if (!class_exists($className, true) || !in_array('npOptimizerBase', class_parents($className)))
    {
      throw new sfConfigurationException(sprintf('Optimizer class "%s" does not exist nor extends the npOptimizerBase class. Please checkout the documentation.', $className));
    }
    
    return new $className($this->dispatcher, $this->configuration[$type]['params'], $this->baseAssetsDir);
  }
}