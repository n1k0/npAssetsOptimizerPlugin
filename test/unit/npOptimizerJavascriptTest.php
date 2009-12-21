<?php

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/optimizer/base/npOptimizerBase.class.php';
require dirname(__FILE__).'/../../lib/optimizer/javascript/npOptimizerJavascript.class.php';

$t = new lime_test(1, new lime_output_color());

// optimizeFile()
$t->diag('optimizeFile()');
$o = new npOptimizerJavascript(new sfEventDispatcher(), array(
  'driver' => 'jsmin',
  'destination' => 'blah',
  'files' => array(),
));
$jsFile = dirname(__FILE__).'/data/foo.js';
$t->cmp_ok(strlen($o->optimizeFile($jsFile)), '<', filesize($jsFile), 'optimizeFile() optimizes js contents');
