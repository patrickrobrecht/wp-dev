<?php
	include_once 'config.php';
	include_once 'functions.php';

	// Update local cache with the latest data from the WordPress API.
	$start_time = microtime( true );

	foreach ( $plugins as $plugin ) {
		get_plugin_file( $plugin, true );
		get_plugin_stats_file( $plugin, true );
		get_plugin_translations_file( $plugin, true );
	}
?>	
	<p><?php echo get_duration_for_output( $start_time, 'File cache updated in %s seconds' ); ?>
