<?php

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/optimizer/base/npOptimizerBase.class.php';
require dirname(__FILE__).'/../../lib/optimizer/stylesheet/npOptimizerStylesheet.class.php';

$t = new lime_test(1, new lime_output_color());

// optimizeFile()
$t->diag('optimizeFile()');
$o = new npOptimizerStylesheet(new sfEventDispatcher(), array(
  'destination' => 'blah',
  'files' => array(),
));
$cssFile = dirname(__FILE__).'/data/foo.css';
$t->cmp_ok(strlen($o->optimizeFile($cssFile)), '<', filesize($cssFile), 'optimizeFile() optimizes css contents');
