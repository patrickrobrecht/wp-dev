<?php 
	include_once 'config.php';
	include_once 'functions.php';	
	
	$plugins = $plugins_show_default;
	$custom_plugins = false;
	if ( isset( $_GET['plugins'] ) ) {
		$regex = '@([a-z]|[0-9]|-|,)+@s';
		$plugins_test = strtolower( $_GET['plugins'] );
		if ( preg_match( $regex, $plugins_test ) ) {
			$plugins = explode( ',', $plugins_test );
			$custom_plugins = true;
		}
	}
	
	if ( isset( $_GET['update'] ) && in_array( $_GET['update'], $plugins) ) {
		$fresh_plugin = $_GET['update'];
	} else {
		$fresh_plugin = '';
	}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>WordPress Plugins Overview</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.tablesorter.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/exporting.js"></script>
</head>
<body>
	<header>
		<h1>
			<?php if ( $custom_plugins || $fresh_plugin != '') { ?>
			<a href="./">WordPress Plugins Overview</a>
			<?php } else { ?>
			WordPress Plugins Overview
			<?php } ?>
		</h1>
		<nav>
			<ul>
				<li><a href="#plugins">Plugins</a></li>
				<li><a href="#versions">Versions</a></li>
				<li><a href="#ratings">Ratings</a></li>
				<li><a href="#translations">Translations</a></li>
			</ul>
		</nav>
		<div id="clear-header"></div>
	</header>
	<main>
		<ul id="messages">
	<?php
		$start_time = microtime( true );
		
		// Get the data from local cache.
		$plugins_data = array();
		$plugins_stats = array();
		$plugins_translations = array();
		$active_languages = array();
		foreach( $plugins as $plugin ) {		
			$plugin_data_file = get_plugin_file( $plugin, $plugin == $fresh_plugin );
			$plugins_data[ $plugin ] = json_decode( $plugin_data_file );
			
			$plugin_stats_file = get_plugin_stats_file( $plugin, $plugin == $fresh_plugin );
			$plugins_stats[ $plugin ] = json_decode( $plugin_stats_file );
			
			$plugin_translations_file = get_plugin_translations_file( $plugin, $plugin == $fresh_plugin );
			$translations = json_decode( $plugin_translations_file )->translations;
			$translations_array = array();
			foreach( $translations as $translation ) {
				$translations_array[ $translation->language ] = $translation;
				$active_languages[ $translation->language] = array( 
						'english_name' => $translation->english_name,
						'native_name' => $translation->native_name
				);
			}
			$plugins_translations[ $plugin ] = $translations_array;
		}
		
		asort( $active_languages );
	?>
		</ul>
		<section>
			<h2 id="plugins">Plugins</h2>
			<table id="table-plugins">
				<thead>
					<tr>
						<th scope="col">Plugin name</th>
						<th scope="col">Author</th>
						<th scope="col">Contributors</th>
						<th scope="col">Latest version</th>
						<th scope="col" colspan="2">Version stats</th>
						<th scope="col">Ratings</th>
						<th scope="col">Downloads</th>
						<th scope="col" colspan="2">Support</th>
						<th scope="col" colspan="3">Development</th>
						<th scope="col">Last updated</th>
						<th scope="col" colspan="2">Translations</th>
						<th scope="col">Cache</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $plugins as $plugin ) {
						$plugin_url = sprintf( 'https://wordpress.org/plugins/%s', $plugin );
						$support_url = sprintf( 'https://wordpress.org/support/plugin/%s', $plugin );
						$support_feed_url = sprintf( 'https://wordpress.org/support/plugin/%s/feed/', $plugin );
						$svn_url = sprintf( 'https://plugins.svn.wordpress.org/%s/', $plugin );
						$trac_url = sprintf( 'https://plugins.trac.wordpress.org/browser/%s/', $plugin );
						$development_log_rss_url = sprintf( 'https://plugins.trac.wordpress.org/log/%s?limit=100&mode=stop_on_copy&format=rss', $plugin );
						$translations_url = sprintf( 'https://translate.wordpress.org/projects/wp-plugins/%s', $plugin );
						
						$latest_version = $plugins_data[ $plugin ]->version;
						$latest_version_array = explode( '.', $latest_version );
						$latest_version_2 = $latest_version_array[0] . '.' . $latest_version_array[1];
						$latest_version_2_stats = $plugins_stats[ $plugin ]->$latest_version_2;
					
						$update_url = sprintf( '?update=%s', $plugin );
						if ( $custom_plugins ) {
							$update_url .= sprintf( '&plugins=%s', $_GET['plugins'] );
						}
					?>
					<tr>
						<td><a href="<?php echo $plugin_url; ?>" target="_blank"><?php echo $plugins_data[ $plugin ]->name; ?></a></td>
						<td><?php echo str_replace( '<a', '<a target="_blank"', $plugins_data[ $plugin ]->author ); ?></td>
						<td><?php $contributors = $plugins_data[ $plugin ]->contributors;
								foreach ( $contributors as $contributor_name => $wordpress_profile_url ) { ?>
							<a href="<?php echo $wordpress_profile_url; ?>" target="_blank"><?php echo $contributor_name; ?></a>	
							<?php } ?></td>
						<td><a href="<?php echo $plugins_data[ $plugin ]->download_link; ?>" target="_blank"><?php echo $latest_version; ?></a></td>
						<td><?php if ( $latest_version_2_stats ) {
									echo sprintf(
											'%.2f %% on %s.x',
											$plugins_stats[ $plugin ]->$latest_version_2,
											$latest_version_2
									);
							}?>
						<td><a href="#chart-versions-<?php echo $plugin; ?>">Stats</a></td>
						<td class="right"><a href="#chart-ratings-<?php echo $plugin; ?>"><?php echo $plugins_data[ $plugin ]->num_ratings; ?></a></td>
						<td class="right"><?php echo number_format( $plugins_data[ $plugin ]->downloaded ); ?>
				
						<td><a href="<?php echo $support_url; ?>" target="_blank">Forum</a></td>
						<td><a href="<?php echo $support_feed_url; ?>" target="_blank">RSS</a></td>
						<td><a href="<?php echo $svn_url; ?>" target="_blank">SVN</a>
						<td><a href="<?php echo $trac_url; ?>" target="_blank">Trac</a></td>
						<td><a href="<?php echo $development_log_rss_url; ?>" target="_blank">Log RSS</a></td>
						<td><?php echo date( 'Y-m-d H:i:s', strtotime( $plugins_data[ $plugin ]->last_updated ) ); ?></td>							
						<td><a href="<?php echo $translations_url; ?>" target="_blank">Translate</a></td>
						<td><a href="#translations-<?php echo $plugin; ?>">Translations</a></td>
						
						<td><a href="<?php echo $update_url; ?>">Refresh cache</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<script>
			$("#table-plugins").tablesorter({
					sortList: [ [0,0] ],
			});
			</script>
		</section>
		<section>
			<h2 id="versions">Installed Versions</h2>
			<?php foreach( $plugins as $plugin ) {
				$versions = $plugins_stats[ $plugin ];
			?>
			<div id="chart-versions-<?php echo $plugin; ?>" class="chart"></div>
			<script>
			jQuery(function() {
				jQuery('#chart-versions-<?php echo $plugin; ?>').highcharts({
					chart: {
						type: 'pie'
					},
					title: {
						text: 'Versions <?php echo $plugins_data[ $plugin ]->name; ?>'
					},
					legend: {
						enabled: true,
					},
					series: [ {
						name: 'Versions',
						data: [ <?php foreach( $versions as $version => $count ) {
									echo "{name: '" . $version . "', y: " . $count . "},";
								} ?> ]
					} ],
					credits: {
						enabled: false
					},
					exporting: {
						filename: 'versions-<?php echo $plugin; ?>'
					}
				});
			});
			</script>		
			<?php } ?>
		</section>
		<section>
			<h2 id="ratings">Ratings</h2>
			<?php foreach( $plugins as $plugin ) {
				$ratings = $plugins_data[ $plugin ]->ratings;
			?>
			<div id="chart-ratings-<?php echo $plugin; ?>" class="chart"></div>
			<script>
			jQuery(function() {
				jQuery('#chart-ratings-<?php echo $plugin; ?>').highcharts({
					chart: {
						type: 'pie'
					},
					title: {
						text: 'Ratings for <?php echo $plugins_data[ $plugin ]->name; ?>'
					},
					legend: {
						enabled: true,
					},
					series: [ {
						name: 'Ratings',
						data: [ <?php foreach( $ratings as $stars => $count ) {
									if ( $count > 0) {
										$stars_string = '1 star';
										if ( intval( $stars ) > 1 ) {
											$stars_string = $stars . ' stars';
										}	
										echo "{name: '" . $stars_string . "', y: " . $count . "},";
									}
								} ?> ]
					} ],
					credits: {
						enabled: false
					},
					exporting: {
						filename: 'ratings-<?php echo $plugin; ?>'
					}
				});
			});
			</script>
		<?php } ?>
		</section>
		<section>
			<h2 id="translations">Translations</h2>	
			<table id="table-translations">
				<thead>
					<tr>
						<th scope="col">English name</th>
						<th scope="col">Native name</th>
						<th scope="col">Language slug</th>
						<?php foreach( $plugins as $plugin ) { ?>
						<th scope="col" id="translations-<?php echo $plugin; ?>"><?php echo sprintf(
								'<a href="https://translate.wordpress.org/projects/wp-plugins/%s" target="_blank">%s</a>',
								$plugin,
								$plugins_data[ $plugin ]->name
							); ?>
							<?php echo $plugins_data[ $plugin ]->version; ?>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $active_languages as $language => $names ) { ?>
					<tr>
						<td><?php echo $names['english_name']; ?></td>
						<td><?php echo $names['native_name']; ?></td>
						<td><?php echo $language; ?></td>
						<?php foreach( $plugins as $plugin ) {
							$translations = $plugins_translations[ $plugin ];
							$plugin_version = $plugins_data[ $plugin ]->version;
							if ( array_key_exists( $language, $translations ) ) {
								$translation = $translations[ $language ];
								if ( $plugin_version == $translation->version ) {
									$class = 'latest';
								} else {
									$class = 'old';
								}
								$text = sprintf(
										'<a href="%s" target="_blank">%s</a>',
										$translation->package,
										$translation->version
								);
							} else {
								$class = 'missing';
								$text = "&mdash;";
							} ?>
						<td class="<?php echo $class; ?>"><?php echo $text; ?></td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<script>
			$("#table-translations").tablesorter({
					sortList: [ [0,0] ],
			});
			</script>
		</section>
	</main>	
	<footer>
		<p>A project by <a href="https://patrick-robrecht.de/">Patrick Robrecht</a>.
			License: GPL v3.
			Source Code: <a href="https://github.com/patrickrobrecht/wp-dev">GitHub</a>.</p>
	</footer>
	<!-- <?php echo get_duration_for_output($start_time, 'Generated in %s seconds.'); ?> -->
</body>
</html>