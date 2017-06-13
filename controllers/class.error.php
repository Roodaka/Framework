<?php
namespace Framework\Controllers;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Error Extends \Framework\Controller
 {
 public function build_header()
   {

   }

  public function main()
   {
    if(\Framework\Core::$error_code === \Framework\Core::ERROR_INVALID_ROUTE) { echo '404: Not found'; }
    elseif(\Framework\Core::$error_code === \Framework\Core::ERROR_LOOP) { echo '508: Loop Detected'; }
    elseif(\Framework\Core::$error_code === \Framework\Core::ERROR_CONTEXT) { echo '401: Unauthorized'; }
    elseif(\Framework\Core::$error_code === \Framework\Core::ERROR_FILE) { echo '503: Service Unavailable'; }
    else { echo '500: Internal Server Error'; }
   }
 }