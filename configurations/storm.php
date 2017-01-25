<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 'blacklist' => array('Hard_Rain::', 'self::', '_SESSION', '_SERVER', '_POST', '_ENV', 'eval', 'exec', 'unlink', 'rmdir'),
 'compiled_directory' => VIEWS_DIR.'cached'.DS.'templates'.DS,
 'html_directory' => VIEWS_DIR.'html'.DS,
 'use_raw_php' => false,
 'compiled_file_header' => '<?php defined(\'ROOT\') or exit(); ?>'."\n",

 // Magia. No tocar.
 'tags' => array(
   'if_open' => '\{if="([^"]*)"\}',
   'elseif' => '\{elseif="([^"]*)"\}',
   'else' => '{else}',
   'if_close' => '{/if}',
   'loop_open' => '\{loop="\$([a-zA-Z0-9_]*)(\-\>\[\')*(.*)\ as \$([a-zA-Z0-9_]*) to \$([a-zA-Z0-9_]*)"\})',
   'loop_close' => '{/loop}',
   'include' => '/\{include="([^"]*)"\}'   
   ));