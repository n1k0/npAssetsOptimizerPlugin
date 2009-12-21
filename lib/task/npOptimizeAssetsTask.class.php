<?php
/**
 * npAssetsOptimizerPlugin assets optimization task 
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  task
 * @author      Nicolas Perriault <nperriault@gmail.com>
 */
class npOptimizeAssetsTask extends sfBaseTask
{
  static protected $types = array('all', 'png_images', 'javascripts', 'stylesheets');
  
  protected $optimizer = null;
  
  /**
   * @see sfTask
   */
  public function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment name', 'dev'),
      new sfCommandOption('type', null, sfCommandOption::PARAMETER_REQUIRED, sprintf('The type of assets to optimize (%s)', implode(', ', self::$types)), $default = 'all'),
    ));
    
    $this->namespace = 'optimize';
    $this->name = 'assets';
    $this->briefDescription = 'Optimizes assets';
  }
  
  /**
   * @see sfTask
   */
  public function execute($arguments = array(), $options = array())
  {
    $configuration = sfConfig::get('app_np_assets_optimizer_plugin_configuration', array());
    
    $this->optimizer = new npAssetsOptimizerService($this->dispatcher, $configuration);
    
    $this->logSection('optimize', $options['type']);
    
    switch ($options['type'])
    {
      case 'all':
        $this->optimizePngImages();
        $this->optimizeJavascripts();
        $this->optimizeStylesheets();
        break;
      case 'png_images':
        $this->optimizePngImages();
        break;
      case 'javascripts':
        $this->optimizeJavascripts();
        break;
      case 'stylesheets':
        $this->optimizeStylesheets();
        break;
      default:
        throw new Exception(sprintf('Unsupported optimization type "%s"; available types are: %s', $options['type'], implode(', ', self::$types)));
        break;
    }
  }
  
  public function optimizePngImages()
  {
    $this->logSection('png_images', 'Optimizing PNG images (this can take a while...)');
    
    $this->logSection('png_images', sprintf('Optimized %d PNG images', count($this->optimizer->optimizePngImages())));
  }
  
  public function optimizeJavascripts()
  {
    $this->logSection('javascripts', 'Optimizing javascripts (this can take a while...)');
    
    $files = $this->optimizer->optimizeJavascripts();
    
    $generated = str_replace(sfConfig::get('sf_web_dir'), 'web', $file = array_pop($files));

    if ($file)
    {
      $this->logSection('javascripts', sprintf('Optimized javascript generated in %s (%d b.)', $generated, filesize($file)));
    }
    else
    {
      $this->logSection('javascripts', 'No optimization made');
    }
  }
  
  public function optimizeStylesheets()
  {
    $this->logSection('stylesheets', 'Optimizing stylesheets (this can take a while...)');
    
    $files = $this->optimizer->optimizeStylesheets();
    
    $generated = str_replace(sfConfig::get('sf_web_dir'), 'web', $file = array_pop($files));

    if ($file)
    {
      $this->logSection('stylesheets', sprintf('Optimized stylesheet generated in %s (%d b.)', $generated, filesize($file)));
    }
    else
    {
      $this->logSection('stylesheets', 'No optimization made');
    }
  }
}