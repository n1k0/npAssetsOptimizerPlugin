<?php

include dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(14, new lime_output_color());

$dispatcher = new sfEventDispatcher();
$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

if (file_exists($rm = sprintf('%s/css/my_optimized.css', $baseAssetsDir)))
{
  unlink($rm);
}

if (file_exists($rm = sprintf('%s/js/my_optimized.js', $baseAssetsDir)))
{
  unlink($rm);
}

// getOptimizer()
$t->diag('getOptimizer()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => true,
    'class' => 'npOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'enabled' => true,
    'class' => 'npOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
), $baseAssetsDir);
$t->isa_ok($service->getOptimizer('javascript'), 'npOptimizerJavascript', 'getOptimizer() retrieves correct js optimizer instance');
$t->isa_ok($service->getOptimizer('stylesheet'), 'npOptimizerStylesheet', 'getOptimizer() retrieves correct css optimizer instance');

// Disabled optimizer
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => false,
    'class' => 'npOptimizerJavascript',
    'params' => array(
    ),
  ),
  'stylesheet' => array(
    'enabled' => false,
    'class' => 'npOptimizerStylesheet',
    'params' => array(
    ),
  ),
), $baseAssetsDir);
$t->is($service->getOptimizer('javascript'), null, 'getOptimizer() retrieves a null if js optimizer is disabed by configuration');
$t->is($service->getOptimizer('stylesheet'), null, 'getOptimizer() retrieves a null if css optimizer is disabed by configuration');

// Custom optimizers
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => true,
    'class' => 'myOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'enabled' => true,
    'class' => 'myOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
), $baseAssetsDir);

try
{
  $service->getOptimizer('javascript');
  $t->fail('getOptimizer() throws an exception if js optimizer class does not exist');
}
catch (sfConfigurationException $e)
{
  $t->pass('getOptimizer() throws an exception if js optimizer class does not exist');  
}

try
{
  $service->getOptimizer('stylesheet');
  $t->fail('getOptimizer() throws an exception if css optimizer class does not exist');
}
catch (sfConfigurationException $e)
{
  $t->pass('getOptimizer() throws an exception if css optimizer class does not exist');  
}

class myNewOptimizerJavascript extends npOptimizerJavascript {}
class myNewOptimizerStylesheet extends npOptimizerStylesheet {}

$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => true,
    'class' => 'myNewOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'enabled' => true,
    'class' => 'myNewOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
), $baseAssetsDir);

$t->isa_ok($service->getOptimizer('javascript'), 'myNewOptimizerJavascript', 'getOptimizer() retrieves correct js optimizer instance');
$t->isa_ok($service->getOptimizer('stylesheet'), 'myNewOptimizerStylesheet', 'getOptimizer() retrieves correct css optimizer instance');

// optimizeJavascripts()
$t->diag('optimizeJavascripts()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => true,
    'class' => 'npOptimizerJavascript',
    'params' => array(
      'destination' => '/js/my_optimized.js',
      'files' => array(
        'foo.js',
      ),
    ),
  ),
), $baseAssetsDir);
try
{
  $service->optimizeJavascripts();
  $t->pass('optimizeJavascripts() can optimize js files');
}
catch (RuntimeException $e)
{
  $t->fail('optimizeJavascripts() can optimize js files');
}

// optimizeStylesheets()
$t->diag('optimizeStylesheets()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'stylesheet' => array(
    'enabled' => true,
    'class' => 'npOptimizerStylesheet',
    'params' => array(
      'destination' => '/css/my_optimized.css',
      'files' => array(
        'foo.css',
      ),
    ),
  ),
), $baseAssetsDir);
try
{
  $service->optimizeStylesheets();
  $t->pass('optimizeStylesheets() can optimize css files');
}
catch (RuntimeException $e)
{
  $t->fail('optimizeStylesheets() can optimize css files');
}

// replaceJavascripts()
$t->diag('replaceJavascripts()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'enabled' => true,
    'class' => 'npOptimizerJavascript',
    'params' => array(
      'destination' => '/js/my_optimized.js',
      'files' => array(
        'foo.js',
      ),
    ),
  ),
), $baseAssetsDir);

$response = new sfWebResponse($dispatcher, array());
$response->addJavascript('foo.js');
$service->replaceJavascripts($response);
$responseJS = array_keys($response->getJavascripts());
$t->is(count($responseJS), 1, 'replaceJavascripts() replaced javascripts');
$t->is($responseJS[0], '/js/my_optimized.js', 'replaceJavascripts() replaced javascripts');

// replaceStylesheets()
$t->diag('replaceStylesheets()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'stylesheet' => array(
    'enabled' => true,
    'class' => 'npOptimizerStylesheet',
    'params' => array(
      'destination' => '/css/my_optimized.css',
      'files' => array(
        'foo.css',
      ),
    ),
  ),
), $baseAssetsDir);

$response = new sfWebResponse($dispatcher, array());
$response->addStylesheet('foo.css');
$service->replaceStylesheets($response);
$responseCSS = array_keys($response->getStylesheets());
$t->is(count($responseCSS), 1, 'replaceStylesheets() replaced stylesheets');
$t->is($responseCSS[0], '/css/my_optimized.css', 'replaceStylesheets() replaced stylesheets');