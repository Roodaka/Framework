<?php
namespace Framework\Controllers;

class Error Extends \Framework\Controller
 {
 public function init() { }

  public function main()
   {
    if(\Framework\Router::$error_code === \Framework\Router::ERROR_INVALID_ROUTE) { echo '404: Not found'; }
    elseif(\Framework\Router::$error_code === \Framework\Router::ERROR_LOOP) { echo '508: Loop Detected'; }
    elseif(\Framework\Router::$error_code === \Framework\Router::ERROR_CONTEXT) { echo '401: Unauthorized'; }
    elseif(\Framework\Router::$error_code === \Framework\Router::ERROR_FILE) { echo '503: Service Unavailable'; }
    else { echo '500: Internal Server Error'; }
   }
 }