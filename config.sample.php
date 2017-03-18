<?php
/**
 * Configuration file:
 * Define the list of plugins shown by default and included in the cron job here.
 *
 * @package wp-dev
 * @since 1.0
 */

// List of plugins to show if no parameters are set.
$plugins_show_default = array(
		// Enter plugin slugs on wordpress.org here.
		'extended-evaluation-for-statify',
		'posts-and-users-stats'
);
sort( $plugins_show_default );

// List of plugins to include in the cron job.
$plugins_cron_job = array_merge(
		$plugins_show_default, // Include all plugins shown by default.
		array( // Enter additional plugin slugs on wordpress.org here.
				'extended-evaluation-for-statify',
				'posts-and-users-stats',
		)
);
sort( $plugins_cron_job );