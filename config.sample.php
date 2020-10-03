<?php

/**
 * Configuration file:
 * Define the list of plugins shown by default and included in the cron job here.
 *
 * @package wp-dev
 * @since 1.0
 */

// Data directory (for caching the data fetched from the WordPress.org API).
$dataDirectory = __DIR__ . '/data';

// List of plugins to include in the cron job.
$pluginsForCronJob = [
    // Enter plugin slugs on wordpress.org here.
    'extended-evaluation-for-statify',
    'posts-and-users-stats',
];
sort($pluginsForCronJob);
