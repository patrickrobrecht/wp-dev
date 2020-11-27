<?php

/**
 * Cron job for updating the data from wordpress.org.
 *
 * @package wp-dev
 */

include_once __DIR__ . '/config.php';
include_once __DIR__ . '/vendor/autoload.php';

use WordPressPluginDashboard\WordPressApi;

// Create required directories.
$directories = [
    $dataDirectory,
    $dataDirectory . '/authors',
    $dataDirectory . '/plugins',
    $dataDirectory . '/stats',
    $dataDirectory . '/translations',
];
foreach ($directories as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0775) && !is_dir($directory)) {
        throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
    }
}

// Update local cache with the latest data from the WordPress API.
$wordPressApi = new WordPressApi();

global $pluginsForCronJob;
foreach ($pluginsForCronJob as $pluginSlug) {
    $wordPressApi->getPluginInfo($pluginSlug, true);
    $wordPressApi->getPluginStats($pluginSlug, true);
    $wordPressApi->getPluginTranslations($pluginSlug, true);
}

global $authorsForCronJob;
foreach ($authorsForCronJob as $authorSlug) {
    $wordPressApi->getAuthor($authorSlug, true);
}
