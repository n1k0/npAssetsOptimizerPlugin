<?php

include dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(7, new lime_output_color());

$dispatcher = new sfEventDispatcher();
$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

class myDriver extends npDriverBase
{
  public function doProcessFile($file, $replace = false)
  {
    return substr(file_get_contents($file), 0, 7);
  }
}

// ->__construct()
$t->diag('->__construct()');

// ->processFile()
$t->diag('->processFile()');
$d = new myDriver();
$file = $baseAssetsDir.'/css/foo.css';
$process = $d->processFile($file);
$t->isa_ok($process, 'myDriver', 'processFile()');

// ->getResults()
$t->diag('->getResults()');
$result = $process->getResults();
$t->is($result['originalSize'], filesize($file), 'processFile() retrieves the correct "originalSize" value');
$t->is($result['optimizedSize'], 7, 'processFile() retrieves the correct "optimizedSize" value');
$t->is($result['ratio'], 12.96, 'processFile() retrieves the correct "ratio" value');
$t->is($result['optimizedContent'], '#myrule', 'processFile() retrieves the correct "optimizedContent" value');

// ->getOptimizationRatio()
$t->diag('->getOptimizationRatio()');
$t->is($process->getOptimizationRatio(), 12.96, 'getOptimizationRatio() retrieves the correct value');

// ->reset()
$t->diag('->reset()');
$process->reset();
try
{
  $result = $process->getResults();
  $t->fail('reset() resets the driver');
}
catch (LogicException $e)
{
  $t->pass('reset() resets the driver');
}
