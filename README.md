# WordPress Plugins Overview

This application provides a [summary for wordpress.org plugins](https://wp-dev.patrick-robrecht.de/) with data accessible via the WordPress.org Plugins API:

* general data, such as author, latest version, number of downloads, URLs of WordPress.org pages
* installed versions
* ratings
* translations via [Translating WordPress](https://translate.wordpress.org/)


## Parameters

You can add the following GET parameters to https://wp-dev.patrick-robrecht.de/:

* `plugins=plugin1,plugin2,plugin3`: If the parameter is a list of slugs of plugin on wordpress.org, the plugin data of those plugins is shown instead of the default ones.
* `update=plugin-slug`: If plugin-slug is one of the plugins from the list (either the custom or the default one), the local cache of data from the wordpress.org API is updated for this plugin.

## How to get your own installation

* Clone this repository.
* Generate the minimized CSS file with `npm run build`.
* Copy `config.sample.php` to `config.php`.
* Edit `config.php` by defining the list of plugins to be shown by default and additional plugins to be updated by `cron.php`.
* Copy all files to your server running PHP.
* Create a daily cron-job for `cron.php` (recommended, but optional).
