<?php

include dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(14, new lime_output_color());

$baseAssetsDir = realpath($np_plugin_dir . '/test/data');

// optimizeFile()
$t->diag('optimizeFile()');
$o = new npOptimizerJavascript(new sfEventDispatcher(), array(
  'driver'      => 'JSMin',
  'destination' => 'blah',
  'files'       => array(),
), $baseAssetsDir);
$jsFile = $np_plugin_dir . '/test/data/js/foo.js';
$result = $o->optimizeFile($jsFile);
$t->isa_ok($result, 'array', 'optimizeFile() returns an array');
$t->ok(array_key_exists('optimizedSize', $result), 'optimizeFile() returns an array containing "optimizedSize" key');
$t->ok(array_key_exists('originalSize', $result), 'optimizeFile() returns an array containing "originalSize" key');
$t->ok(array_key_exists('ratio', $result), 'optimizeFile() returns an array containing "ratio" key');
$t->is($result['originalSize'], filesize($jsFile), 'optimizeFile() has coherent results');
$t->cmp_ok($result['optimizedSize'], '<', $result['originalSize'], 'optimizeFile() optimizes js contents');

$t->diag('optimize()');
// Natural order
$o = new npOptimizerJavascript(new sfEventDispatcher(), array(
  'driver'      => 'JSMinPlus',
  'destination' => '/js/my_combined.js',
  'files'       => array('1.js', '2.js', '3.js'),
), $baseAssetsDir);
$result = $o->optimize($jsFile);
$t->isa_ok($result, 'array', 'optimize() returns an array');
$t->is(count($result), 2, 'optimize() returns two keys');
$t->ok(array_key_exists('generatedFile', $result), 'optimize() returns an "generatedFile" key');
$t->is($result['generatedFile'], $baseAssetsDir.'/js/my_combined.js', 'optimize() returns the expected "generatedFile" result');
$t->ok(array_key_exists('statistics', $result), 'optimize() returns a "statistics" key');
$t->is(file_get_contents($baseAssetsDir.'/js/my_combined.js'), 'var run1=function(){alert(1)}var run2=function(){alert(2)}var run3=function(){alert(3)}', 'optimize() generated the correct optimized file contents');
$t->is(array_keys($result['statistics']), array(
  $baseAssetsDir.'/js/1.js',
  $baseAssetsDir.'/js/2.js',
  $baseAssetsDir.'/js/3.js',
), 'optimize() returns correct "statistics" keys');

// Different order
$o = new npOptimizerJavascript(new sfEventDispatcher(), array(
  'driver'      => 'JSMinPlus',
  'destination' => '/js/my_combined.js',
  'files'       => array('3.js', '2.js', '1.js'),
), $baseAssetsDir);
$result = $o->optimize($jsFile);
$t->is(file_get_contents($baseAssetsDir.'/js/my_combined.js'), 'var run3=function(){alert(3)}var run2=function(){alert(2)}var run1=function(){alert(1)}', 'optimize() generated the correct optimized file contents, in the specified order');
