<?php
/**
 * Functions for updating the data from wordpress.org.
 *
 * @package wp-dev
 * @since 1.0
 */

/**
 * Returns the update message.
 *
 * @param string $type the type of update.
 * @param string $plugin string the slug of the plugin.
 * @param bool $copied whether the copying was successful.
 *
 * @return string the update message
 */
function get_update_message( $type, $plugin, $copied ) {
	if ( $copied ) {
		$message = '<li><span class="success">Updated</span>: %s file for %s</li>';
	} else {
		$message = '<li><span class="error">Error</span>: could not update %s file for %s</li>';
	}
	return sprintf( $message, $type, $plugin );
}

/**
 * Returns the plugin file.
 * If the file is not in the cache or its too old, it is downloaded from wordpress.org.
 *
 * @param string $plugin the slug of the plugin.
 * @param bool $update if true the plugin is updated from wordpress.org, even if its not too old.
 *
 * @return string the file content
 */
function get_plugin_file( $plugin, $update = false ) {
	$file_cache = dirname( __FILE__ ) . '/data/plugins/' . $plugin . '.json';
	if ( $update || ! file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
			'https://api.wordpress.org/plugins/info/1.0/%s.json?fields=active_installs',
			$plugin
		);
		$copied = copy( $api_url, $file_cache );
		echo get_update_message( 'plugin', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

/**
 * Returns the plugin version statistics file.
 * If the file is not in the cache or its too old, it is downloaded from wordpress.org.
 *
 * @param string $plugin the slug of the plugin.
 * @param bool $update if true the plugin is updated from wordpress.org, even if its not too old.
 *
 * @return string the file content
 */
function get_plugin_stats_file( $plugin, $update = false ) {
	$file_cache = dirname( __FILE__ ) . '/data/plugins-stats/' . $plugin . '.json';
	if ( $update || ! file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
			'https://api.wordpress.org/stats/plugin/1.0/%s',
			$plugin
		);
		$copied = copy( $api_url, $file_cache );
		echo get_update_message( 'plugin stats', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

/**
 * Returns the plugin translations file.
 * If the file is not in the cache or its too old, it is downloaded from wordpress.org.
 *
 * @param string $plugin the slug of the plugin.
 * @param bool $update if true the plugin is updated from wordpress.org, even if its not too old.
 *
 * @return string the file content
 */
function get_plugin_translations_file( $plugin, $update = false ) {
	$file_cache = dirname( __FILE__ ) . '/data/plugins-translations/' . $plugin . '.json';
	if ( $update || ! file_exists( $file_cache ) || is_old( $file_cache ) ) {
		$api_url = sprintf(
			'https://api.wordpress.org/translations/plugins/1.0/?slug=%s',
			$plugin
		);
		$copied = copy( $api_url, $file_cache );
		echo get_update_message( 'plugin translations', $plugin, $copied );
	}
	return file_get_contents( $file_cache );
}

/**
 * Returns the duration from the given start time to now.
 *
 * @param float $start_time the start time.
 *
 * @return float the time from the start time to now
 */
function get_duration( $start_time ) {
	$end_time = microtime( true );
	return $end_time - $start_time;
}

/**
 * Formats the duration for output.
 *
 * @param float $start_time the start time.
 * @param string $text the text to display (must include %s).
 *
 * @return string the string to output
 */
function get_duration_for_output( $start_time, $text = 'Executed in %s seconds' ) {
	return sprintf( $text, number_format( get_duration( $start_time ), 2 ) );
}

/**
 * Checks whether the file with the given name is older than 24 hours.
 *
 * @param string $file the file name.
 *
 * @return bool true if older than 24 hours, else false
 */
function is_old( $file ) {
	$lastchanged = filectime( $file );
	return ( date( 'U' ) - $lastchanged ) > ( 24 * 60 * 60 );
}
