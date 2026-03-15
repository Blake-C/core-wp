/**
 * Webpack Entry Points
 * --------------------
 * Each key becomes a separate output bundle: assets/js/bundle.[name].js
 * Webpack also enqueues that file in WordPress via functions.php.
 *
 * When to add a new entry here
 * ----------------------------
 * Add a new entry when you need a standalone script file — for example a
 * page-specific script that should never load globally:
 *
 *   'home-scripts': './theme_components/js/home-scripts.js',
 *   'checkout':     './theme_components/js/checkout.js',
 *
 * Then enqueue the output file conditionally in functions.php:
 *
 *   if ( is_front_page() ) {
 *     wp_enqueue_script( 'core-wp-home', get_template_directory_uri() . '/assets/js/bundle.home-scripts.js', [], core_wp_cache_bust( $path ), true );
 *   }
 *
 * When to use dynamic import() chunks instead
 * --------------------------------------------
 * For components that are only needed when a certain element is on the page,
 * prefer a dynamic import() inside _components.js rather than a new entry here.
 * Webpack splits those automatically — no entry registration needed.
 * See theme_components/js/modules/_components.js for the pattern.
 */

const scriptsList = {
	'global-scripts': './theme_components/js/global-scripts.js',
}

export default scriptsList
