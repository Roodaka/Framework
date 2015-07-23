<?php

final class Hard_Rain
 {
  private static $configuration = array(
   'blacklist' => array('\$this', 'Hard_Rain::', 'self::', '_SESSION', '_SERVER', '_ENV', 'eval', 'exec', 'unlink', 'rmdir'),
   'compile_directory' => '../../cache/html/',
   'html_directory' => '../../views/html/',
   'css_directory' => '../../views/html/css/',
   'javascript_directory' => '../../views/html/js/',
   '' => '');

  private static $variables = array();

  const PHP_OPEN_TAG = '<'.'?php';
  const PHP_CLOSE_TAG = '?'.'>';

  const REGEXP_IF_OPEN
  const REGEXP_IF_CLOSE = '';

  const REGEXP_LOOP_OPEN = '/\{loop(?: name){0,1}="\${0,1}([^"]*)"\}/';
  const REGEXP_LOOP_CLOSE = '{/loop}';

  const REGEXP_INCLUDE = '/\{include="([^"]*)"(?: cache="([^"]*)"){0,1}\}/';

  const LOOPVARS_KEY = '$key';
  const LOOPVARS_VALUE = '$value';
  const LOOPVARS_COUNTER = '$counter';
 }