<?php

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/optimizer/base/npOptimizerBase.class.php';

$t = new lime_test(10, new lime_output_color());

$webDir = sfConfig::get('sf_web_dir');

class npOptimizerBaseTest extends npOptimizerBase
{
  public function getAssetFilepath($file) { return $file; }
  public function optimizeFile($file) { return $file; }
}

// computeAssetFilepath()
$t->diag('computeAssetFilepath()');
$o = new npOptimizerBaseTest();
// js
$t->is($o->computeAssetFilepath('main', 'js', '/js'), sprintf('%s/js/main.js', $webDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('main.js', 'js', '/js'), sprintf('%s/js/main.js', $webDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('/javascript/main.js', 'js', '/javascript'), sprintf('%s/javascript/main.js', $webDir), 'computeAssetFilepath() retrieves js asset filepath');
$t->is($o->computeAssetFilepath('http://toto.com/main.js', 'js'), null, 'computeAssetFilepath() does not retrieve invalid js asset filepath');
$t->is($o->computeAssetFilepath('https://toto.com/main.js', 'js'), null, 'computeAssetFilepath() does not retrieve invalid js asset filepath');
// css
$t->is($o->computeAssetFilepath('main', 'css', '/css'), sprintf('%s/css/main.css', $webDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('main.css', 'css', '/css'), sprintf('%s/css/main.css', $webDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('/stylesheet/main.css', 'css', '/javascript'), sprintf('%s/stylesheet/main.css', $webDir), 'computeAssetFilepath() retrieves css asset filepath');
$t->is($o->computeAssetFilepath('http://toto.com/main.css', 'css'), null, 'computeAssetFilepath() does not retrieve invalid css asset filepath');
$t->is($o->computeAssetFilepath('https://toto.com/main.css', 'css'), null, 'computeAssetFilepath() does not retrieve invalid css asset filepath');