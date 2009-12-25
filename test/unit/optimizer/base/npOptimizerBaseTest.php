<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(10, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

class npOptimizerBaseTest extends npOptimizerBase
{
  public function getAssetFilepath($file) { return $file; }
}

// computeAssetFilepath()
$t->diag('computeAssetFilepath()');
// js
$o = new npOptimizerBaseTest(new sfEventDispatcher(), array('driver' => 'JSMin'), $baseAssetsDir);
$t->is($o->computeAssetFilepath('main', 'js', '/js'), sprintf('%s/js/main.js', $baseAssetsDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('main.js', 'js', '/js'), sprintf('%s/js/main.js', $baseAssetsDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('/javascript/main.js', 'js', '/javascript'), sprintf('%s/javascript/main.js', $baseAssetsDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('http://toto.com/main.js', 'js'), null, 'computeAssetFilepath() does not retrieve invalid js asset filepath');
$t->is($o->computeAssetFilepath('https://toto.com/main.js', 'js'), null, 'computeAssetFilepath() does not retrieve invalid js asset filepath');
// css
$o = new npOptimizerBaseTest(new sfEventDispatcher(), array('driver' => 'Cssmin'), $baseAssetsDir);
$t->is($o->computeAssetFilepath('main', 'css', '/css'), sprintf('%s/css/main.css', $baseAssetsDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('main.css', 'css', '/css'), sprintf('%s/css/main.css', $baseAssetsDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('/stylesheet/main.css', 'css', '/javascript'), sprintf('%s/stylesheet/main.css', $baseAssetsDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('http://toto.com/main.css', 'css'), null, 'computeAssetFilepath() does not retrieve invalid css asset filepath');
$t->is($o->computeAssetFilepath('https://toto.com/main.css', 'css'), null, 'computeAssetFilepath() does not retrieve invalid css asset filepath');
