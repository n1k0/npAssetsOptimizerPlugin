<?php
/**
 * npAssetsOptimizerPlugin web debug toolbar panel class
 * 
 * @package     npAssetsOptimizerPlugin
 * @subpackage  debug
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */

class npAssetsOptimizerWebDebugPanel extends sfWebDebugPanel
{
  /**
   * @see sfWebDebugPanel
   */
  public function getTitle()
  {
    if ($this->isEnabled())
    {
      return '<img src="/npAssetsOptimizerPlugin/images/debug.png" alt="Assets Optimizer" height="16" width="16" /> optimizer';
    }
  }

  /**
   * @see sfWebDebugPanel
   */
  public function getPanelTitle()
  {
    return 'Assets Optimizer Status';
  }
  
  /**
   * Shows information related to which files are currently being optimized
   * 
   * @see sfWebDebugPanel
   */
  public function getPanelContent()
  {
    return 
    '<ul>
       <li><a href="?_disable_assets_optimization=0">Disable Assets Optimization</a></li>
     </ul>'
    ;
  }
  
  /**
   * Shortcut for determining if the assets optimizer is enabled
   * 
   * @return boolean
   */
  protected function isEnabled()
  {
    return true;
    return sfConfig::get('app_np_assets_optimizer_plugin_enabled', false);
  }
}
