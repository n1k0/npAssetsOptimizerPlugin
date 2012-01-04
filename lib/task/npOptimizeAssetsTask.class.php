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
  static protected $types = array('all', 'javascript', 'png_image', 'stylesheet', 'jpeg_image');

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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment name', 'prod'),
      new sfCommandOption('type', null, sfCommandOption::PARAMETER_REQUIRED, sprintf('The type of assets to optimize (%s)', implode(', ', self::$types)), $default = 'all'),
    ));

    $this->namespace = 'optimize';
    $this->name = 'assets';
    $this->briefDescription = 'Optimizes assets';
    $this->detailedDescription = <<<EOF
The [optimize:assets|INFO] task optimizes javascript, stylesheet
PNG  and JPEG image files as configured for the provided symfony application
name. For optimizations configured in the [frontend|INFO] app.yml file:

  [php symfony optimize:assets frontend|INFO]

For optimizations configured in the [backend|INFO] app.yml file:

  [php symfony optimize:assets backend|INFO]

To only optimize javascript assets:

  [php symfony optimize:assets frontend --type=javascript|INFO]

To only optimize stylesheet assets:

  [php symfony optimize:assets frontend --type=stylesheet|INFO]

To only optimize PNG images:

  [php symfony optimize:assets frontend --type=png_image|INFO]

To only optimize JPEG images:

  [php symfony optimize:assets frontend --type=jpeg_image|INFO]

Of course, if you configured assets optimization only for a given
environement, you can target it by using the [--type|INFO] option:

  [php symfony optimize:assets frontend --type=all --env=prod|INFO]
EOF;
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
        $this->logSection($section, sprintf('compressed %s: %s', $statistic['ratio'].'%', $this->formatTaskFilePath($file)));
      }
    }

    if (isset($results['generatedFile']))
    {
      $generated = $this->formatTaskFilePath($results['generatedFile']);

      $this->logSection($section, sprintf('Optimized combined asset generated in %s (%db.)', $generated, filesize($results['generatedFile'])));
    }
    else if (in_array($section, array('stylesheet', 'javascript')))
    {
      $this->logSection($section, 'No optimization made');
    }
  }

  protected function formatTaskFilePath($assetPath)
  {
    return str_replace(sfConfig::get('sf_web_dir'), 'web', $assetPath);
  }
}
