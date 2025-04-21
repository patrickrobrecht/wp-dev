<?php

/**
 * Dashboard about plugins from the WordPress.org Plugin Directory.
 * The page displays general information on the plugin, installed versions and ratings as well as
 * available translations via translate.wordpress.org.
 *
 * @package wp-dev
 */

use WordPressPluginDashboard\WordPressPluginDashboard;

include_once __DIR__ . '/config.php';
include_once __DIR__ . '/vendor/autoload.php';

$startTime = microtime(true);
$dashboard = new WordPressPluginDashboard();
$formsClass = count($dashboard->getPlugins()) > 0 ? ' class="hide"' : '';
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>WordPress Plugins Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>
<body>
    <header>
        <h1><a href="./">WordPress Plugins Dashboard</a></h1>
    </header>
    <main>
        <p>You can create your own dashboard by adding a parameter to the URL:</p>
        <ul>
            <li><code>/?plugins=s,s2</code> for WordPress plugin slugs <code>s</code>, <code>s2</code>
                (as in <code>https://wordpress.org/plugins/s/</code>)</li>
            <li>or <code>/?authors=a,b</code> with <code>a</code>, <code>b</code>
                being a username on WordPress.org to list all the author's plugins.</li>
        </ul>

        <ul id="messages">
            <?php foreach ($dashboard->getMessages() as $message) { ?>
                <li><span class="<?php echo $message->getClass(); ?>"><?php echo $message->getText(); ?></span></li>
            <?php } ?>
        </ul>

        <button id="toggle-button">Select plugins for your custom dashboard</button>
        <section id="forms"<?php echo $formsClass; ?>>
            <div class="columns full-width">
                <div class="column">
                    <div id="plugin-inputs">
                        <label for="plugin-slugs">Plugins (slugs from WordPress.org)</label>
                        <?php foreach ($dashboard->getPluginSlugs() as $pluginSlug) { ?>
                            <input class="slug-input" type="text" name="plugin-slugs" value="<?php echo $pluginSlug; ?>">
                        <?php } ?>
                        <input class="slug-input" type="text" name="plugin-slugs">
                    </div>
                    <button class="add-row" type="button"
                            data-container="plugin-inputs"
                            data-template="plugins-input-template">
                        Add another plugin
                    </button>
                    <template id="plugins-input-template">
                        <input class="slug-input" type="text" name="plugin-slugs">
                    </template>
                </div>

                <div class="column">
                    <div id="author-inputs">
                        <label for="author-slugs">Author (WordPress.org username)</label>
                        <?php foreach ($dashboard->getAuthorSlugs() as $authorSlug) { ?>
                            <input class="slug-input" type="text" name="author-slugs" value="<?php echo $authorSlug; ?>">
                        <?php } ?>
                        <input class="slug-input" type="text" name="author-slugs">
                    </div>
                    <button class="add-row" type="button"
                            data-container="author-inputs"
                            data-template="author-input-template">
                        Add another author
                    </button>
                    <template id="author-input-template">
                        <input class="slug-input" type="text" name="author-slugs" value="">
                    </template>
                </div>
            </div>

            <form method="get" class="full-width">
                <input id="plugin-slugs" name="plugins" type="hidden" value="<?php echo implode(',', $dashboard->getPluginSlugs()); ?>">
                <input id="author-slugs" name="authors" type="hidden" value="<?php echo implode(',', $dashboard->getAuthorSlugs()); ?>">
                <button>Show dashboard</button>
            </form>
        </section>

<?php if (count($dashboard->getPlugins()) > 0) { ?>
        <section>
            <h2 id="plugins">Plugins</h2>
            <table id="table-plugins">
                <thead>
                    <tr>
                        <th colspan="5">General information</th>
                        <th colspan="2">Versions</th>
                        <th colspan="3">Compatibility</th>
                        <th colspan="8">Ratings</th>
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
                        <th scope="col" role="columnheader">
                            <abbr title="unanswered bad reviews from last six months (maximum last 30 reviews)">
                                1-2★ no answer
                            </abbr>
                        </th>

                        <th scope="col" role="columnheader">threads</th>
                        <th scope="col" role="columnheader">threads unresolved</th>
                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>

                        <th scope="col" role="columnheader">Language packs</th>
                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>

                        <th scope="col" class="no-sort" data-sort-method="none">Links</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dashboard->getPlugins() as $plugin) {
                        $timeSinceLastUpdate = date_diff($plugin->getLastUpdated(), date_create());
                        $updatedInLastYear = $timeSinceLastUpdate->y < 1;
                        if ($updatedInLastYear) {
                            $lastUpdateTimeDescription = sprintf('%d days ago', $timeSinceLastUpdate->days);
                        } else {
                            $lastUpdateTimeDescription = sprintf(
                                '%d %s %d %s ago',
                                $timeSinceLastUpdate->y,
                                $timeSinceLastUpdate->y === 1 ? 'year' : 'years',
                                $timeSinceLastUpdate->m,
                                $timeSinceLastUpdate->m === 1 ? 'month' : 'months',
                            );
                        }
                        $lastUpdateTitle = date_format($plugin->getLastUpdated(), 'd.m.Y H:i:s') . ', ' . $lastUpdateTimeDescription;

                        $versionInfo = [];
                        foreach ($plugin->getVersionStats() as $version => $count) {
                            $versionInfo[] = sprintf('%.2f %% on %s.x', $count, $version);
                        }

                        $isCompatibleWithLatestWordPressVersion = version_compare(
                            $plugin->getMaxWordPressVersion(),
                            $dashboard->getLatestWordPressVersion(),
                            ">="
                        );

                        $averageRating = $plugin->getRatingAverage();
                        if ($averageRating >= 4) {
                            $averageRatingClass = 'positive';
                        } elseif ($averageRating <= 2) {
                            $averageRatingClass = 'negative';
                        }

                        $badReviewsNotAnswered = $plugin->getBadReviewsNotAnswered();
                        ?>
                    <tr>
                        <td>
                            <a href="<?php echo $plugin->getPluginUrl(); ?>" target="_blank"><?php echo $plugin->getName(); ?></a>
                            <form method="post" class="inline">
                                <input name="update" type="hidden" value="<?php echo $plugin->getSlug(); ?>">
                                <button type="submit" class="link">🔃</button>
                            </form>
                        </td>
                        <td><?php echo str_replace('<a', '<a target="_blank"', $plugin->getAuthor()); ?></td>
                        <td class="<?php echo $updatedInLastYear ? 'positive' : 'negative' ?>"
                            data-sort="<?php echo date_format($plugin->getLastUpdated(), 'U'); ?>">
                            <time title="<?php echo $lastUpdateTitle; ?>">
                                <?php echo date_format($plugin->getLastUpdated(), 'd.m.Y'); ?>
                            </time>
                        </td>
                        <td class="right"><?php echo number_format($plugin->getActiveInstallsCount()); ?>+</td>
                        <td class="right"><?php echo number_format($plugin->getDownloadCount()); ?></td>

                        <td><a href="<?php echo $plugin->getDownloadUrl(); ?>" target="_blank"><?php echo $plugin->getVersion(); ?></a></td>
                        <td><?php echo implode('<br>', $versionInfo); ?></td>

                        <td><?php echo $plugin->getMinWordPressVersion() ?: 'unknown'; ?></td>
                        <td class="<?php echo $isCompatibleWithLatestWordPressVersion ? 'positive' : 'negative'; ?>">
                            <?php echo $plugin->getMaxWordPressVersion() ?: 'unknown'; ?>
                        </td>
                        <td><?php echo $plugin->getMinPhpVersion() ?: 'unknown'; ?></td>

                        <td class="right"><?php echo number_format($plugin->getRatingCount()); ?></td>
                        <?php foreach (range(1, 5) as $i) { ?>
                            <td class="right"><?php echo number_format($plugin->getRatings($i)); ?></td>
                        <?php } ?>
                        <td class="right<?php echo $averageRatingClass ? ' ' . $averageRatingClass : ''; ?>">
                            <?php echo number_format($averageRating, 1); ?>
                        </td>
                        <td class="right <?php echo $badReviewsNotAnswered > 0 ? 'negative' : 'positive'; ?>">
                            <?php echo number_format($badReviewsNotAnswered); ?>
                        </td>

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
        </section>
<?php } ?>

<?php if (count($dashboard->getLanguages()) > 0) { ?>
        <section>
            <h2 id="translations">Translations (language packs)</h2>
            <table id="table-translations">
                <thead>
                    <tr>
                        <th scope="col">English name</th>
                        <th scope="col">Native name</th>
                        <th scope="col">Code</th>
                        <?php foreach ($dashboard->getPlugins() as $plugin) { ?>
                        <th scope="col" id="translations-<?php echo $plugin->getSlug(); ?>">
                            <a href="<?php echo $plugin->getTranslateUrl(); ?>" target="_blank"><?php echo $plugin->getName(); ?></a>
                            <?php echo $plugin->getVersion(); ?>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dashboard->getLanguages() as $language) { ?>
                    <tr>
                        <td><?php echo $language->getEnglishName(); ?></td>
                        <td><?php echo $language->getNativeName(); ?></td>
                        <td><?php echo $language->getLocaleCode(); ?></td>
                        <?php foreach ($dashboard->getPlugins() as $plugin) {
                            $translations = $plugin->getTranslations();
                            $pluginVersion = $plugin->getVersion();
                            if (array_key_exists($language->getLocaleCode(), $translations)) {
                                $translation = $translations[$language->getLocaleCode()];
                                $class = $pluginVersion === $translation['version'] ? 'positive' : 'negative';
                                $text = sprintf('<a href="%s" target="_blank">%s</a>', $translation['package'], $translation['version']);
                            } else {
                                $class = 'negative';
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
                        <?php foreach ($dashboard->getPlugins() as $plugin) { ?>
                            <th class="right"><?php echo number_format($plugin->getTranslationsCount()); ?></th>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
        </section>
<?php } ?>
    </main>
    <footer>
        <p>A project by <a href="https://patrick-robrecht.de/">Patrick Robrecht</a>.
            <a href="https://github.com/patrickrobrecht/wp-dev">Source Code</a> licensed unter GPL v3.</p>
   </footer>
    <!-- <?php echo sprintf('Generated in %s seconds.', number_format(microtime(true) - $startTime, 5)) ?> -->
    <script src="assets/js/lib/tablesort.min.js"></script>
    <script src="assets/js/lib/tablesort.number.min.js"></script>
    <script src="assets/js/functions.min.js"></script>
</body>
</html>
