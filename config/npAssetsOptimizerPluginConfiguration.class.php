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
    
    $this->dispatcher->connect('debug.web.load_panels', array($this, 'listenToLoadDebugWebPanelEvent'));
    
    $this->dispatcher->connect('request.filter_parameters', array($this, 'listenToRequestFilterParameterEvent'));
    
    sfConfig::set('sf_standard_helpers', array_merge(sfConfig::get('sf_standard_helpers', array()), array('npOptimizer')));
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
    
    if (!class_exists($serviceClass = sfConfig::get('app_np_assets_optimizer_plugin_class', 'npAssetsOptimizerService')))
    {
      throw new sfConfigurationException(sprintf('The %s service class does not exist', $serviceClass));
    }
    
    $configuration = sfConfig::get('app_np_assets_optimizer_plugin_configuration', array());
    $assetsOptimizer = new $serviceClass($context->getEventDispatcher(), $configuration);
    
    $context->set('assets_optimizer', $assetsOptimizer);
  }
  
  /** 
   * Listens on the debug.web.load_panels event and adds the web debug panel
   * 
   * @param sfEvent $event The event object for the debug.web.load_panel event
   */
  public function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('assets_optimizer', new npAssetsOptimizerWebDebugPanel($event->getSubject()));
  }
  
  /** 
   * Listens on the request.filter_parameters to catch the "_disable_assets_optimization" parameter
   * 
   * @param sfEvent $event The event object for the debug.web.load_panel event
   */
  public function listenToRequestFilterParameterEvent(sfEvent $event, $parameters)
  {
    $request = $event->getSubject();
    
    if ($request->hasParameter('_disable_assets_optimization'))
    {
      sfConfig::set('app_np_assets_optimizer_plugin_enabled', 1 === (int) $request->getGetParameter('_disable_assets_optimization', 0) ? false : sfConfig::get('app_np_assets_optimizer_plugin_enabled', false));
    }
    
    return $parameters;
  }
}
