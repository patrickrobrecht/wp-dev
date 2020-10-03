<?php

/**
 * Cron job for updating the data from wordpress.org.
 *
 * @package wp-dev
 */

include_once __DIR__ . '/config.php';
include_once __DIR__ . '/vendor/autoload.php';

use WordPressPluginDashboard\WordPressApi;

// Update local cache with the latest data from the WordPress API.
$wordPressApi = new WordPressApi(false);

global $pluginsForCronJob;
foreach ($pluginsForCronJob as $pluginSlug) {
    $wordPressApi->getPluginInfo($pluginSlug, true);
    $wordPressApi->getPluginStats($pluginSlug, true);
    $wordPressApi->getPluginTranslations($pluginSlug, true);
}
