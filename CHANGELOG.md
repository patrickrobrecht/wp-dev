# WordPress Plugins Dashboard - Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## Version 1.3.0 (2020-10-21)

### Added
- Add positive/negative indicators for last updated, compatibility with the latest WP version, average rating
- Add cron job to update authors specified in the configuration file

### Changed
- Rename the `author` parameter to `authors` and allow multiple authors to be chosen
- Refactor code (move logic from the main file and API class into controller class)


## Version 1.2.0 (2020-10-03)

### Added
- Add commands for PSR-12 code style check
- Add a form for creating a custom dashboard instead of default plugins
- Allow to list all plugins of a WordPress.org user via the `author` parameter
- Minify JavaScript

### Changed
- Refactor code according to PSR-12 code style
- Integrate version stats and ratings into the table
- Replace library for sorting table columns
- Remove the diagrams and the jQuery dependency


## Version 1.1.1 (2020-05-01)

### Changed
- Update JavaScript dependencies
- Add source map file for the CSS
- Fix display on mobile screen


## Version 1.1.0 (2020-03-12)

### Added
- Setup dependency management with npm and buld with gulp

### Changed
- Update JavaScript dependencies


## Version 1.0.1 (2017-03-19)

### Changed
* Update [Tablesorter](https://github.com/Mottie/tablesorter) library to latest version 2.28.5 (minimized JavaScript file now)
* Bugfix: sorting for active installs column
* Minimized CSS file


## Version 1.0.0 (2017-03-18)

### Added
* Display information on WordPress.org plugins and their translations
* Custom configuration via `config.php`
* Update plugin data via cron job or manually
* Auto-update of plugin data if older than 24 hours
