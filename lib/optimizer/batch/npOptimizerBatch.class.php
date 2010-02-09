<?php
/**
 * npAssetsOptimizerPlugin batch optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizerBatch extends npOptimizerBase
{
  protected 
    $drivers = array();
  
  /**
   * @see npOptimizerBase
   */
  public function configure(array $configuration = array())
  {
    parent::configure($configuration);
    
    if (!isset($configuration['drivers']))
    {
      throw new sfConfigurationException('The "drivers" configuration parameter is mandatory in order to use a batch optimizer');
    }
    
    $this->setDrivers($configuration['drivers']);
  }
  
  public function optimize()
  {
    
  }
  
  public function optimizeFile($file)
  {
    
  }
  
  protected function setDrivers(array $drivers = array())
  {
    if (!count($drivers))
    {
      throw new InvalidArgumentException('No drivers specified');
    }
    
    foreach ($drivers as $driver)
    {
      // TODO
    }
  }
}