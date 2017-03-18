<?php
/**
 * Cron job for updating the data from wordpress.org.
 *
 * @package wp-dev
 * @since 1.0
 */

include_once 'config.php';
include_once 'functions.php';

// Update local cache with the latest data from the WordPress API.
$start_time = microtime( true );

foreach ( $plugins_cron_job as $plugin ) {
    get_plugin_file( $plugin, true );
    get_plugin_stats_file( $plugin, true );
    get_plugin_translations_file( $plugin, true );
}
?>	
<p><?php echo get_duration_for_output( $start_time, 'File cache updated in %s seconds' ); ?>
