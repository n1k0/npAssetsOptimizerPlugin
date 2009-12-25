<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(4, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

// __construct()
$t->diag('__construct()');
try
{
  $o = new npOptimizerPngImage(new sfEventDispatcher(), array(), $baseAssetsDir);
  $t->fail('__construct() throws an exception if no driver is specified');
}
catch (sfConfigurationException $e)
{
  $t->pass('__construct() throws an exception if no driver is specified');
}

// configure()
$t->diag('configure()');
try
{
  $o = new npOptimizerPngImage(new sfEventDispatcher(), array(
    'driver' => 'Pngout',
  ), $baseAssetsDir);
  $t->fail('configure() throws an exception if no files/folder option is specified');
}
catch (sfConfigurationException $e)
{
  $t->pass('configure() throws an exception if no files/folder option is specified');
}

// findPngImages()
$t->diag('findPngImages()');
$o = new npOptimizerPngImage(new sfEventDispatcher(), array(
  'driver' => 'Pngout',
  'folders' => array($np_plugin_dir . '/test/data/images'),
), $baseAssetsDir);
$found = $o->findPngImages(array($np_plugin_dir . '/test/data/images'));
$t->is(count($found), 1, 'findPngImages() finds PNG images');
$t->is($found[0], realpath($np_plugin_dir . '/test/data/images/test-pilot.png'), 'findPngImages() finds a PNG image');
