<?php
return array(
 'blacklist' => array('Hard_Rain::', 'self::', '_SESSION', '_SERVER', '_POST', '_ENV', 'eval', 'exec', 'unlink', 'rmdir'),
 'compiled_directory' => APP_PATH.'cached/templates/',
 'html_directory' => APP_PATH.'views/html/',
 'use_raw_php' => false,
 'compiled_file_header' => '<?php defined(\'APP_PATH\') or exit(); ?>'."\n",

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