# WordPress Plugins Dashboard

The [dashboard](https://wp-dev.patrick-robrecht.de/) provides an overview on plugins listed in the official plugin directory
    on WordPress.org:

- plugin information, such as the author, the latest version, the number of downloads, the URLs of WordPress.org pages,
    and much more
- statistics on installed versions, ratings and support threads on WordPress.org
- translations via [Translating WordPress](https://translate.wordpress.org/)

The web application written in PHP fetches the data via the WordPress.org Plugins API.

## How to create your custom dashboard
You can add the following GET parameters to https://wp-dev.patrick-robrecht.de/ to specify which plugins are shown:
- `plugins=plugin1,plugin2,plugin3` to show the plugins with the respective slugs on WordPress.org
- `authors=author1,author2` to show all the author's plugins (the author must be specified using the WordPress.org username)

## How to get your own installation
- Clone this repository.
- Generate the minimized CSS and JavaScript files with `npm run build`.
- Copy `config.sample.php` to `config.php`.
- Edit `config.php` to define the data directory (and optionally the plugins/authors to be updated by the cronjob).
- Copy all files to your server running PHP.
- Create a daily cron-job for `cron.php` (recommended, but optional).
    To create the data directories, cron.php needs to be started once.

## How to develop
Required: Composer and npm
- Install dependencies via `composer install` and `npm install`.
- Use `npm run build` to create/update the minimized CSS and JavaScript files.
- Use `composer cs` to check the PHP code for PSR-12 compatibility, and `composer csfix` to fix issues automatically.
