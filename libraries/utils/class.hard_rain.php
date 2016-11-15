<?php

final class Hard_Rain
 {
  private static $configuration = array(
   'blacklist' => array('Hard_Rain::', 'self::', '_SESSION', '_SERVER', '_ENV', 'eval', 'exec', 'unlink', 'rmdir'),
   'compile_directory' => '../../views/cached/',
   'html_directory' => '../../views/html/',
   'use_raw_php' => false);

  private static $variables = array();
  private static $templates = array();

  const TEMPLATE_EXT = '.html';
  const CACHE_EXT = '.tmp';

  const PHP_OPEN_TAG = '<?php';
  const PHP_CLOSE_TAG = '?>';
  const REGEXP_OPEN_TAG = '/\{{';
  const REGEXP_CLOSE_TAG ='\}}/';
  
  const REGEXP_IF_OPEN = '/\{if="([^"]*)"\}/';
  const REGEXP_ELSEIF = '/\{elseif="([^"]*)"\}/';
  const REGEXP_ELSE = '{else}';
  const REGEXP_IF_CLOSE = '{/if}';

  const REGEXP_LOOP_OPEN = '/\{loop="\$([a-zA-Z0-9_]*)(\-\>\[\')*(.*)\ as \$([a-zA-Z0-9_]*) to \$([a-zA-Z0-9_]*)"\}';
  const REGEXP_LOOP_CLOSE = '{/loop}';
  
  const REGEXP_CLOSE_TAG = '}';

  const REGEXP_INCLUDE = '/\{include="([^"]*)"\}/';


  public static function parse($template_name, $cache = true, $echo = true)
   {
    $filename = self::$configuration['html_directory'].$template_name;
    if(is_file(self::$configuration['html_directory'].$template_name.self::CACHE_EXT) === true && DEV === false)
     {
      
     }
    if(is_file(self::$configuration['html_directory'].$template_name.self::FILE_EXTENSION) === true)
     {
      $content = file_get_contents(self::$configuration['html_directory'].$template_name.self::FILE_EXTENSION);
      
     }
    throw new \Framework\Hard_Rain_Exception('No se ha encontrado la plantilla '.$template_name.'.');
   }



  private static function parse_simples($content) {}
  private static function parse_conditions($content) {}
  private static function parse_loop($content) {}
  private static function parse_include($content) {}





  public static function add_template($template_name)
   {
    if(is_array($template_name) === true)
     {
      self::$templates += $template_name;
     }
    else
     {
      self::$templates[] = $template_name;
     }
   }



  public static function add_var($key, $value = null)
   {
    if(is_array($key) === true && $value === null)
     {
      self::$variables += $key;
     }
    else
     {
      self::$variables[$key] = $value;
     }
   }

  private static function is_in_blacklist($key)
   {
    if(in_array($key, self::blacklist) === false)
     {
      return false;
     }
    else
     {
      throw new Hard_Rain_Exception('La clave '.$key.', llamada en la l&iacute;nea '.self::$parsing_line.' no est&aacute; permitida.');
     }
   }
 }

class Hard_Rain_Exception extends \Exception { }