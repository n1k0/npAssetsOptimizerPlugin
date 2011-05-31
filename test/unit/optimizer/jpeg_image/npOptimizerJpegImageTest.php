<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(3, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

// __construct()
$t->diag('__construct()');
try
{
  $o = new npOptimizerJpegImage(new sfEventDispatcher(), array(), $baseAssetsDir);
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
  $o = new npOptimizerJpegImage(new sfEventDispatcher(), array(
    'driver' => 'Jpegtran',
  ), $baseAssetsDir);
  $t->fail('configure() throws an exception if no files/folder option is specified');
}
catch (sfConfigurationException $e)
{
  $t->pass('configure() throws an exception if no files/folder option is specified');
}

// findJpegImages()
$t->diag('findJpegImages()');
$o = new npOptimizerJpegImage(new sfEventDispatcher(), array(
  'driver' => 'Jpegtran',
  'folders' => array($np_plugin_dir . '/test/data/images'),
), $baseAssetsDir);
$found = $o->findJpegImages(array($np_plugin_dir . '/test/data/images'));
$t->is(count($found), 2, 'findJpegImages() finds JPEG images, no matter if file extension is jpeg or jpg');
