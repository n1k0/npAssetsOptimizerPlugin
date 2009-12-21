<?php

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/service/npAssetsOptimizerService.class.php';

$t = new lime_test(12, new lime_output_color());

$dispatcher = new sfEventDispatcher();

// getOptimizer()
$t->diag('getOptimizer()');
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'class' => 'npOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'class' => 'npOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
));
$t->isa_ok($service->getOptimizer('javascript'), 'npOptimizerJavascript', 'getOptimizer() retrieves correct js optimizer instance');
$t->isa_ok($service->getOptimizer('stylesheet'), 'npOptimizerStylesheet', 'getOptimizer() retrieves correct css optimizer instance');

// Custom optimizers
$service = new npAssetsOptimizerService($dispatcher, array(
  'javascript' => array(
    'class' => 'myOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'class' => 'myOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
));

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
    'class' => 'myNewOptimizerJavascript',
    'params' => array(
      'destination' => '/foo',
    ),
  ),
  'stylesheet' => array(
    'class' => 'myNewOptimizerStylesheet',
    'params' => array(
      'destination' => '/bar',
    ),
  ),
));

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
));
try
{
  $service->optimizeJavascripts();
  $t->fail('optimizeJavascripts() cannot optimize unexistent files');
}
catch (RuntimeException $e)
{
  $t->pass('optimizeJavascripts() cannot optimize unexistent files');
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
));
try
{
  $service->optimizeStylesheets();
  $t->fail('optimizeStylesheets() cannot optimize unexistent files');
}
catch (RuntimeException $e)
{
  $t->pass('optimizeStylesheets() cannot optimize unexistent files');
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
));

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
));

$response = new sfWebResponse($dispatcher, array());
$response->addStylesheet('foo.css');
$service->replaceStylesheets($response);
$responseCSS = array_keys($response->getStylesheets());
$t->is(count($responseCSS), 1, 'replaceStylesheets() replaced stylesheets');
$t->is($responseCSS[0], '/css/my_optimized.css', 'replaceStylesheets() replaced stylesheets');