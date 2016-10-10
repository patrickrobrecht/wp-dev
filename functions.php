<?php
// Functions.

function get_update_message( $type, $plugin, $copied ) {
	if ( $copied ) {
		$message = '<p>Updated local cache: %s file for %s</p>';
	} else {
		$message = '<p>No local update: %s file for %s</p>';
	}
	return sprintf( $message, $type, $plugin );
}

function get_plugin_file( $plugin, $update = false ) {
	$file_cache = 'data/plugins/' . $plugin . '.json';
	if ( $update || !file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
				'https://api.wordpress.org/plugins/info/1.0/%s.json',
				$plugin
		);
		$copied = copy( $api_url, $file_cache );
		echo get_update_message( 'plugin', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

function get_plugin_stats_file( $plugin, $update = false ) {
	$file_cache = 'data/plugins-stats/' . $plugin . '.json';
	if ( $update || !file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
				'https://api.wordpress.org/stats/plugin/1.0/%s',
				$plugin
		);
		$copied = copy( $api_url, $file_cache );
		echo get_update_message( 'plugin stats', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

function get_plugin_translations_file( $plugin, $update = false ) {
	$file_cache = 'data/plugins-translations/' . $plugin . '.json';
	if ( $update || !file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
				'https://api.wordpress.org/translations/plugins/1.0/?slug=%s',
				$plugin
		);
		$copied = copy( $api_url, $file_cache );
		
		echo get_update_message( 'plugin translations', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

function get_duration( $start_time ) {
	$end_time = microtime( true );
	return $end_time - $start_time;
}

function get_duration_for_output( $start_time, $text = 'Executed in %s seconds' ) {
	return sprintf( $text, number_format( get_duration( $start_time ), 2 ) );
}

function is_old( $file ) {
	$lastchanged = filectime ( $file );
	return ( date( 'U' ) - $lastchanged ) > ( 24 * 60 * 60 );
}
