<?php

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/optimizer/base/npOptimizerBase.class.php';
require dirname(__FILE__).'/../../lib/optimizer/javascript/npOptimizerJavascript.class.php';

$t = new lime_test(1, new lime_output_color());

$webDir = sfConfig::get('sf_web_dir');

// getJavascriptFilepath()
$t->diag('getJavascriptFilepath()');
$o = new npOptimizerJavascript();
