# WordPress Plugins Dashboard

This application provides a [summary for wordpress.org plugins](https://wp-dev.patrick-robrecht.de/)
    with data accessible via the WordPress.org Plugins API:

* plugin information, such as author, latest version, number of downloads, URLs of WordPress.org pages
* statistics on installed versions and ratings
* translations via [Translating WordPress](https://translate.wordpress.org/)


## Parameters

You can add the following GET parameters to https://wp-dev.patrick-robrecht.de/:

* `plugins=plugin1,plugin2,plugin3`: If the parameter is a list of slugs of plugins on wordpress.org,
    the plugin data of those plugins the dashboard shows them instead of the default ones.
* `author=name`: If name is a username on WordPress.org, the dashboard shows all the author's plugins.

## How to get your own installation

* Clone this repository.
* Generate the minimized CSS file with `npm run build`.
* Copy `config.sample.php` to `config.php`.
* Edit `config.php` by defining the list of plugins to be shown by default and additional plugins to be updated by `cron.php`.
* Copy all files to your server running PHP.
* Create a daily cron-job for `cron.php` (recommended, but optional).
