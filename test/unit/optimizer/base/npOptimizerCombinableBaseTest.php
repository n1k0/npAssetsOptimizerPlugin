<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(4, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

class npOptimizerCombinableTest extends npOptimizerCombinableBase
{
  public function getAssetFilepath($file) { return $this->baseAssetsDir.'/'.$file; }
}

// ->getOptimizedFileWebPath()
$t->diag('->getOptimizedFileSystemPath()');
$o = new npOptimizerCombinableTest(new sfEventDispatcher(), array(
  'driver'      => 'JSMinPlus',
  'destination' => '/css/my_optimized.css',
  'timestamp'   => false,
), $baseAssetsDir);
$t->is($o->getOptimizedFileWebPath(), '/css/my_optimized.css', 'getOptimizedFileWebPath() retrieves correct web path');

$o = new npOptimizerCombinableTest(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => '/css/my_optimized.css',
  'timestamp'   => true,
  'files' => array('css/foo.css'),
), $baseAssetsDir);
$o->optimize();
$t->like($o->getOptimizedFileWebPath(), '#/css/my_optimized.css\?\d+#', 'getOptimizedFileWebPath() retrieves correct web path with timestamp');

// ->getOptimizedFileSystemPath()
$t->diag('->getOptimizedFileSystemPath()');
$o = new npOptimizerCombinableTest(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => '/css/my_optimized.css',
  'timestamp'   => true,
  'files' => array('css/foo.css'),
), $baseAssetsDir);
$o->optimize();
$t->is($o->getOptimizedFileSystemPath(), $baseAssetsDir.'/css/my_optimized.css', 'getOptimizedFileSystemPath() retrieves correct optimized file path');

// ->generateTimestampedAssetName()
$t->diag('->generateTimestampedAssetName()');
$o = new npOptimizerCombinableTest(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => '/css/my_optimized.css',
  'timestamp'   => true,
  'files' => array('css/foo.css'),
), $baseAssetsDir);
$o->optimize();
$t->is($o->generateTimestampedAssetName(), sprintf('/css/my_optimized.css?%d', filemtime($baseAssetsDir.'/css/my_optimized.css')), 'generateTimestampedAssetName() generates correct timestamped web name');
