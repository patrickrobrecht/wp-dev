<?php
// Configuration.

// List of plugins to show if no parameters are set.
$plugins_show_default = array(
		// Enter plugin slugs on wordpress.org here.
		'extended-evaluation-for-statify',
		'posts-and-users-stats'
);
sort( $plugins_show_default );

// List of plugins to include in the cron job
$plugins_cron_job = array_merge(
		$plugins_show_default, // include all plugins shown by default
		array( // Enter additional plugin slugs on wordpress.org here.
				'extended-evaluation-for-statify',
				'posts-and-users-stats'
		)
);
sort( $plugins_cron_job );