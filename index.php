<?php

/**
 * Dashboard about plugins from the WordPress.org Plugin Directory.
 * The page displays general information on the plugin, installed versions and ratings as well as
 * available translations via translate.wordpress.org.
 *
 * @package wp-dev
 */

use WordPressPluginDashboard\Plugin;
use WordPressPluginDashboard\WordPressApi;

include_once __DIR__ . '/config.php';
include_once __DIR__ . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>WordPress Plugins Dashboard</title>
    <link rel="stylesheet" href="css/dist/style.css">
    <script src="js/tablesort.min.js"></script>
    <script src="js/tablesort.number.min.js"></script>
</head>
<body>
    <header>
        <h1><a href="./">WordPress Plugins Dashboard</a></h1>
        <nav>
            <ul>
                <li><a href="#plugins">Plugins</a></li>
                <li><a href="#translations">Translations</a></li>
            </ul>
        </nav>
        <div id="clear-header"></div>
    </header>
    <main>
        <p>You can create your own dashboard by adding a parameter to the URL:</p>
        <ul>
            <li><code>/?plugins=s,s2</code> for WordPress plugin slugs <code>s</code>, <code>s2</code>
                (as in <code>https://wordpress.org/plugins/s/</code>)</li>
            <li>or <code>/?author=a</code> with <code>a</code> being a username on WordPress.org to list all the author's plugins.</li>
        </ul>
        <ul id="messages">
<?php
$startTime = microtime(true);

$wordPressApi = new WordPressApi(true);
$regex = '@([a-z]|[0-9]|-|,)+@s';

$pluginSlugs = $plugins_show_default;
$customPlugins = false;
if (isset($_GET['author'])) {
	$authorSlugFromRequest = strtolower($_GET['author']);
	if (preg_match($regex, $authorSlugFromRequest)) {
		$jsonFileContents = $wordPressApi->getAuthor($authorSlugFromRequest);
		$json = json_decode($jsonFileContents, true);
		if (isset($json['plugins'])) {
			$pluginSlugs = array_map(
				static function ($i) {
					return $i['slug'];
				},
				$json['plugins']
			);
		}
		$customPlugins = true;
	}
} elseif (isset($_GET['plugins'])) {
    $pluginSlugsFromRequest = strtolower($_GET['plugins']);
    if (preg_match($regex, $pluginSlugsFromRequest)) {
        $pluginSlugs = explode(',', $pluginSlugsFromRequest);
        $customPlugins = true;
    }
}

if (isset($_POST['update']) && in_array($_POST['update'], $pluginSlugs, true)) {
	$freshPluginSlug = $_POST['update'];
} else {
	$freshPluginSlug = '';
}

$languages = [];
$plugins = [];
foreach ($pluginSlugs as $pluginSlug) {
    $plugin = new Plugin($pluginSlug, $pluginSlug === $freshPluginSlug, $wordPressApi);
    $plugins[] = $plugin;
    foreach ($plugin->getTranslations() as $translation) {
        $languages[$translation['language']] = [
            'english_name' => $translation['english_name'],
            'native_name'  => $translation['native_name'],
        ];
    }
}
asort($languages);
?>
        </ul>
        <section>
            <h2 id="plugins">Plugins</h2>
            <table id="table-plugins">
                <thead>
                    <tr>
                        <th colspan="5">General information</th>
                        <th colspan="2">Versions</th>
                        <th colspan="3">Compatibility</th>
                        <th colspan="7">Ratings</th>
                        <th colspan="3">Support</th>
                        <th colspan="2">Translations</th>
                        <th>Development</th>
                    </tr>
                    <tr data-sort-method='thead'>
                        <th scope="col" role="columnheader">Plugin name</th>
                        <th scope="col" role="columnheader">Author</th>
                        <th scope="col" role="columnheader">Last updated</th>
                        <th scope="col" role="columnheader">Active installs</th>
                        <th scope="col" role="columnheader">Downloads</th>

                        <th scope="col" role="columnheader" data-sort-method="dotsep">Latest</th>
                        <th scope="col" role="columnheader">Stats</th>

                        <th scope="col" role="columnheader" data-sort-method="dotsep">
                            <abbr title="minimum required WordPress version">WP min</abbr>
                        </th>
                        <th scope="col" role="columnheader" data-sort-method="dotsep">
                            <abbr title="maximum compatible WordPress version">WP max</abbr>
                        </th>
                        <th scope="col" role="columnheader" data-sort-method="dotsep">
                            <abbr title="minimum required PHP version">PHP min</abbr>
                        </th>

                        <th scope="col" role="columnheader"><abbr title="total number of ratings">Σ</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="number of 1 star ratings">1★</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="number of 2 star ratings">2★</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="number of 3 star ratings">3★</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="number of 4 star ratings">4★</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="number of 5 star ratings">5★</abbr></th>
                        <th scope="col" role="columnheader"><abbr title="average rating">⌀</abbr></th>

                        <th scope="col" role="columnheader">threads</th>
                        <th scope="col" role="columnheader">threads unresolved</th>
                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>

                        <th scope="col" role="columnheader">Language packs</th>
                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>

                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plugins as $plugin) {
                        $versionInfo = [];
                        foreach ($plugin->getVersionStats() as $version => $count) {
                            $versionInfo[] = sprintf('%.2f %% on %s.x', $count, $version);
                        }
                        ?>
                    <tr>
                        <td>
                            <a href="<?php echo $plugin->getPluginUrl(); ?>" target="_blank"><?php echo $plugin->getName(); ?></a>
                            <form method="post">
                                <input name="update" type="hidden" value="<?php echo $plugin->getSlug(); ?>">
                                <button type="submit">🔃</button>
                            </form>
                        </td>
                        <td><?php echo str_replace('<a', '<a target="_blank"', $plugin->getAuthor()); ?></td>
                        <td data-sort="<?php echo date('U', $plugin->getLastUpdated()); ?>">
                            <time title="<?php echo date('Y-m-d H:i:s', $plugin->getLastUpdated()); ?>">
                                <?php echo date('d.m.Y', $plugin->getLastUpdated()); ?>
                            </time>
                        </td>
                        <td class="right"><?php echo number_format($plugin->getActiveInstallsCount()); ?>+</td>
                        <td class="right"><?php echo number_format($plugin->getDownloadCount()); ?></td>

                        <td><a href="<?php echo $plugin->getDownloadUrl(); ?>" target="_blank"><?php echo $plugin->getVersion(); ?></a></td>
                        <td><?php echo implode('<br>', $versionInfo); ?></td>

                        <td><?php echo $plugin->getMinWordPressVersion() ?: 'unknown'; ?></td>
                        <td><?php echo $plugin->getMaxWordPressVersion() ?: 'unknown'; ?></td>
                        <td><?php echo $plugin->getMinPhpVersion() ?: 'unknown'; ?></td>

                        <td class="right"><?php echo number_format($plugin->getRatingCount()); ?></td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class="right"><?php echo number_format($plugin->getRatings($i)); ?></td>
                        <?php } ?>
                        <td class="right"><?php echo number_format($plugin->getRatingAverage(), 1); ?></td>

                        <td class="right"><?php echo number_format($plugin->getSupportThreadCount()); ?></td>
                        <td class="right <?php echo $plugin->getSupportThreadCountUnresolved() > 0 ? 'negative' : 'positive' ?>">
                            <?php echo number_format($plugin->getSupportThreadCountUnresolved()); ?>
                        </td>
                        <td>
                            <a href="<?php echo $plugin->getSupportForumUrl(); ?>" target="_blank">Forum</a>
                            <a href="<?php echo $plugin->getSupportFeedUrl(); ?>" target="_blank">RSS</a>
                        </td>

                        <td class="right">
                            <a href="#translations-<?php echo $plugin->getSlug(); ?>">
                                <?php echo number_format($plugin->getTranslationsCount())?>
                            </a>
                        </td>
                        <td><a href="<?php echo $plugin->getTranslateUrl(); ?>" target="_blank">Translate</a></td>

                        <td>
                            <a href="<?php echo $plugin->getSvnUrl(); ?>" target="_blank">SVN</a>
                            <a href="<?php echo $plugin->getTracUrl(); ?>" target="_blank">Trac</a>
                            <a href="<?php echo $plugin->getTracFeedUrl(); ?>" target="_blank">RSS</a>
                        </td>
                    </tr>
                    <?php } ?>
               </tbody>
            </table>
            <script>
                new Tablesort(document.getElementById('table-plugins'), {
                    descending: true
                });
            </script>
        </section>
     <section>
            <h2 id="translations">Translations (language packs)</h2>
            <table id="table-translations">
                <thead>
                    <tr>
                        <th scope="col">English name</th>
                        <th scope="col">Native name</th>
                        <th scope="col">Code</th>
                        <?php foreach ($plugins as $plugin) { ?>
                        <th scope="col" id="translations-<?php echo $plugin->getSlug(); ?>">
                            <a href="<?php echo $plugin->getTranslateUrl(); ?>" target="_blank"><?php echo $plugin->getName(); ?></a>
                            <?php echo $plugin->getVersion(); ?>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($languages as $language => $names) { ?>
                    <tr>
                        <td><?php echo $names['english_name']; ?></td>
                        <td><?php echo $names['native_name']; ?></td>
                        <td><?php echo $language; ?></td>
                        <?php foreach ($plugins as $plugin) {
                            $translations = $plugin->getTranslations();
                            $pluginVersion = $plugin->getVersion();
                            if (array_key_exists($language, $translations)) {
                                $translation = $translations[$language];
                                $class = $pluginVersion === $translation['version'] ? 'latest' : 'old';
                                $text = sprintf('<a href="%s" target="_blank">%s</a>', $translation['package'], $translation['version']);
                            } else {
                                $class = 'missing';
                                $text = "&mdash;";
                            } ?>
                        <td class="<?php echo $class; ?>"><?php echo $text; ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row" colspan="3">Number of language packs</th>
                        <?php foreach ($plugins as $plugin) { ?>
                        <th class="right"><?php echo number_format($plugin->getTranslationsCount()); ?></th>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
            <script>
                new Tablesort(document.getElementById('table-translations'), {
                    descending: true
                });
            </script>
        </section>
    </main>
    <footer>
        <p>A project by <a href="https://patrick-robrecht.de/">Patrick Robrecht</a>.
            <a href="https://github.com/patrickrobrecht/wp-dev">Source Code</a> licensed unter GPL v3.</p>
   </footer>
    <!-- <?php echo sprintf('Generated in %s seconds.', number_format(microtime(true) - $startTime, 5)) ?> -->
</body>
</html>
