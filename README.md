# WordPress Plugin Development

[This application](https://wp-dev.patrick-robrecht.de/) provides a summary for wordpress.org plugins with data accessible via the Plugins API.

## Parameters

You can add the following GET parameters to https://wp-dev.patrick-robrecht.de/:

* `plugins=plugin1,plugin2,plugin3`: If the parameter is a list of slugs of plugin on wordpress.org, the plugin data of those plugins is shown instead of the default ones.
* `update=plugin-slug`: If plugin-slug is one of the plugins from the list (either the custom or the default one), the local cache of data from the wordpress.org API is updated for this plugin.
