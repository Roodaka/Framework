<?php defined('ROOT') or exit(); ?>  <div class='footer'>
<?php if( DEVELOPER_MODE === true ){ ?>
<span><b><?php echo $lang["dev_time"];?></b>: <?php echo round(microtime(true) - SYSTEM_INIT_TIME, 2); ?></span>
<span><b><?php echo $lang["dev_ram"];?></b>: <?php echo round((memory_get_usage() - SYSTEM_INIT_RAM) / 1024, 2); ?>kb</span>
<span><b><?php echo $lang["dev_sql"];?></b>: <?php echo Framework\Database::$count; ?></span>
<span><b><?php echo $lang["dev_cache"];?></b>: <?php echo round(Framework\Cache::size() / 1024, 2); ?>kb <small><a href='<?php echo url('home', 'clear'); ?>'><?php echo $lang["dev_cache_clear"];?></a></small></span>
<?php } ?>
</div>
<script src='views/html/js/base.js'></script>
</body>
</html>