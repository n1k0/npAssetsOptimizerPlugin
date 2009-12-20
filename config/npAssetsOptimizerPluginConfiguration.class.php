<?php
/**
 * npAssetsOptimizerPlugin configuration file
 * 
 * @package     npAssetsOptimizerPlugin
 * @subpackage  config
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npAssetsOptimizerPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_np_assets_optimizer_plugin_enabled', false))
    {
      $this->dispatcher->connect('context.load_factories', array($this, 'listenToContextLoadFactoriesEvent'));
    }
  }
  
  /**
   * Listens to the "context.load_factories" event to add a configured "assets_optimizer" 
   * service to the current context object instance.
   *
   * @param  sfEvent  $event  An event with an sfContext instance as the subject
   */
  public function listenToContextLoadFactoriesEvent(sfEvent $event)
  {
    $context = $event->getSubject();
    
    $configuration = sfConfig::get('app_np_assets_optimizer_plugin_configuration', array());
    $assetsOptimizer = new npAssetsOptimizerService($context->getEventDispatcher(), $configuration);
    
    $context->set('assets_optimizer', $assetsOptimizer);
  }
}
