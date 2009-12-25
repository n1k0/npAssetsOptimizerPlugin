<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(14, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

// optimizeFile()
$t->diag('optimizeFile()');
$o = new npOptimizerStylesheet(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => 'blah',
  'files'       => array(),
), $baseAssetsDir);
$cssFile = $np_plugin_dir . '/test/data/css/foo.css';
$result = $o->optimizeFile($cssFile);
$t->isa_ok($result, 'array', 'optimizeFile() returns an array');
$t->ok(array_key_exists('optimizedSize', $result), 'optimizeFile() returns an array containing "optimizedSize" key');
$t->ok(array_key_exists('originalSize', $result), 'optimizeFile() returns an array containing "originalSize" key');
$t->ok(array_key_exists('ratio', $result), 'optimizeFile() returns an array containing "ratio" key');
$t->is($result['originalSize'], filesize($cssFile), 'optimizeFile() has coherent results');
$t->cmp_ok($result['optimizedSize'], '<', $result['originalSize'], 'optimizeFile() optimizes css contents');

$t->diag('optimize()');
// Natural order
$o = new npOptimizerStylesheet(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => '/css/my_combined.css',
  'files'       => array('1.css', '2.css', '3.css'),
), $baseAssetsDir);
$result = $o->optimize($cssFile);
$t->isa_ok($result, 'array', 'optimize() returns an array');
$t->is(count($result), 2, 'optimize() returns two keys');
$t->ok(array_key_exists('generatedFile', $result), 'optimize() returns an "generatedFile" key');
$t->is($result['generatedFile'], $baseAssetsDir.'/css/my_combined.css', 'optimize() returns the expected "generatedFile" result');
$t->ok(array_key_exists('statistics', $result), 'optimize() returns a "statistics" key');
$t->is(file_get_contents($baseAssetsDir.'/css/my_combined.css'), '#rule1{border:none}#rule1 a{font-weight:bold}#rule2{border:none}#rule2 p{font-weight:bold}#rule3{}', 'optimize() generated the correct optimized file contents');
$t->is(array_keys($result['statistics']), array(
  $baseAssetsDir.'/css/1.css', 
  $baseAssetsDir.'/css/2.css', 
  $baseAssetsDir.'/css/3.css',
), 'optimize() returns correct "statistics" keys');

// Different order
$o = new npOptimizerStylesheet(new sfEventDispatcher(), array(
  'driver'      => 'Cssmin',
  'destination' => '/css/my_combined.css',
  'files'       => array('3.css', '2.css', '1.css'),
), $baseAssetsDir);
$result = $o->optimize($cssFile);
$t->is(file_get_contents($baseAssetsDir.'/css/my_combined.css'), '#rule3{}#rule2{border:none}#rule2 p{font-weight:bold}#rule1{border:none}#rule1 a{font-weight:bold}', 'optimize() generated the correct optimized file contents, in the specified order');
