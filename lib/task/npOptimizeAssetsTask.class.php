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
  static protected $types = array('all', 'javascript', 'png_image', 'stylesheet');
  
  protected $optimizer = null;
  
  /**
   * @see sfTask
   */
  public function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
    ));
    
    $this->addOptions(array(
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
    $serviceConfiguration = sfConfig::get('app_np_assets_optimizer_plugin_configuration', array());
    
    $this->optimizerService = new npAssetsOptimizerService($this->dispatcher, $serviceConfiguration);
    
    $this->logSection('optimizing', $options['type']);
    
    if (!in_array($options['type'], self::$types))
    {
      throw new Exception(sprintf('Unsupported optimization type "%s"; available types are: %s', $options['type'], implode(', ', self::$types)));
    }
    if ('all' === $options['type'])
    {
      foreach (self::$types as $type)
      {
        if ('all' !== $type)
        {
          $this->optimize($type);
        }
      }
    }
    else
    {
      $this->optimize($options['type']);
    }
  }
  
  public function optimize($type)
  {
    if (is_null($optimizer = $this->optimizerService->getOptimizer($type)))
    {
      $this->logSection('skipped', sprintf('%s optimization service is disabled for the "%s" env.', 
                                           ucfirst($type), $this->configuration->getEnvironment()), null, 'COMMENT');
      
      return;
    }
    
    $this->logSection($type, sprintf('Optimizing %ss using "%s" driver (this can take a while...)', $type, $optimizer->getDriverName()));
    
    $this->logResults($type, $optimizer->optimize());
  }
  
  protected function logResults($section, array $results = array())
  {
    if (!isset($results['statistics']))
    {
      throw new RuntimeException('No statistics have been generated');
    }
    
    foreach ($results['statistics'] as $file => $statistic)
    {
      if (100 === (int) $statistic['ratio'])
      {
        $this->logSection($section, sprintf('already optimized: %s', $this->formatTaskFilePath($file)));
      }
      else
      {
        $this->logSection($section, sprintf('packed %s: %s', $statistic['ratio'].'%', $this->formatTaskFilePath($file)));
      }
    }
    
    if (isset($results['generatedFile']))
    {
      $generated = $this->formatTaskFilePath($results['generatedFile']);
      
      $this->logSection($section, sprintf('Optimized combined asset generated in %s (%db.)', $generated, filesize($results['generatedFile'])));
    }
    else
    {
      $this->logSection($section, 'No optimization made');
    }
  }
  
  protected function formatTaskFilePath($assetPath)
  {
    return str_replace(sfConfig::get('sf_web_dir'), 'web', $assetPath);
  }
}