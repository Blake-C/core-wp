<?php
/**
 * Core WP functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @link https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-files-and-folders
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function core_wp_setup() {
		/*
		* Make theme available for translation.
		*/
		load_theme_textdomain( 'core_wp', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * Custom thumbnail sizes.
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_image_size/
		 *
		 * Ex:
		 * add_image_size( 'unique_name', 490, 240, array( 'center', 'top' ) );
		 */

		/**
		 * This theme uses wp_nav_menu() in one location.
		 */
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'core_wp' ),
				'footer'  => __( 'Footer Menu', 'core_wp' ),
			)
		);

		/**
		 * Opt into WordPress core block styles for better out-of-box block rendering.
		 * Color palette and global styles are defined in theme.json.
		 */
		add_theme_support( 'wp-block-styles' );

		/**
		 * Use HTML5 markup for core WordPress output.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		/**
		 * Allow admins to upload a custom logo via Appearance → Customize.
		 * Use get_custom_logo() or the_custom_logo() in templates.
		 */
		add_theme_support( 'custom-logo' );

		/**
		 * Remove wp_header meta
		 */
		remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds.
		remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed.
		remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link.
		remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
		remove_action( 'wp_head', 'index_rel_link' ); // index link.
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link.
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Display relational links for the posts adjacent to the current post.
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); // Injects rel=shortlink into the head if a shortlink is defined for the current page.
		remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version.
	}

}
add_action( 'after_setup_theme', 'core_wp_setup' );


/**
 * Remove wp version meta tag and from rss feed
 *
 * @link https://thomasgriffin.io/hide-wordpress-meta-generator-tag/
 */
add_filter( 'the_generator', '__return_false' );


/**
 * Add callback for custom TinyMCE editor stylesheets.
 *
 * @link https://developer.wordpress.org/reference/functions/add_editor_style/
 */
add_editor_style( 'assets/css/editor-styles.min.css' );



if ( ! function_exists( 'core_wp_scripts' ) ) {
	/**
	 * Enqueue scripts and styles.
	 */
	function core_wp_scripts() {
		/* Asset file paths set to variables */
		$global_styles  = get_template_directory_uri() . '/assets/css/global-styles.min.css';
		$global_scripts = get_template_directory_uri() . '/assets/js/bundle.global-scripts.js';

		/* Import CSS (Sass files are in the theme-components folder) */
		wp_enqueue_style( 'core-wp-style', $global_styles, array(), core_wp_cache_bust( $global_styles ) );

		/* Import Scripts (Keep to a minimum or import into global-scripts.js file) */
		wp_enqueue_script( 'core-wp-global', $global_scripts, array( 'jquery' ), core_wp_cache_bust( $global_scripts ), true );

		/**
		 * Conditionally add scripts and styles to pages with template tags
		 *
		 * @link https://codex.wordpress.org/Template_Tags
		 *
		 * Ex:
		 * if ( is_front_page() ) {
		 *     $scripts_home = get_template_directory_uri() . '/assets/js/scripts-home-min.js';
		 *     wp_enqueue_script( 'core-wp-home-scripts', $scripts_home, array('jquery'), core_wp_cache_bust( $scripts_home ), true );
		 * }
		 */
	}
}
add_action( 'wp_enqueue_scripts', 'core_wp_scripts' );


if ( ! function_exists( 'core_wp_site_icons' ) ) {
	/**
	 * Output favicon and PWA icon meta tags in <head>.
	 *
	 * Place the icon files generated by https://realfavicongenerator.net
	 * into theme_components/icons/ so they are copied to assets/icons/ on build.
	 *
	 * @link https://realfavicongenerator.net
	 */
	function core_wp_site_icons() {
		$icons_uri = get_template_directory_uri() . '/assets/icons';
		$site_name = esc_attr( get_bloginfo( 'name' ) );

		$tags  = '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $icons_uri . '/apple-touch-icon.png' ) . '">' . "\n";
		$tags .= '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $icons_uri . '/favicon-32x32.png' ) . '">' . "\n";
		$tags .= '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( $icons_uri . '/favicon-16x16.png' ) . '">' . "\n";
		$tags .= '<link rel="manifest" href="' . esc_url( $icons_uri . '/site.webmanifest' ) . '">' . "\n";
		$tags .= '<link rel="mask-icon" href="' . esc_url( $icons_uri . '/safari-pinned-tab.svg' ) . '" color="#5bbad5">' . "\n";
		$tags .= '<link rel="shortcut icon" href="' . esc_url( $icons_uri . '/favicon.ico' ) . '">' . "\n";
		$tags .= '<meta name="msapplication-TileColor" content="#232c97">' . "\n";
		$tags .= '<meta name="msapplication-config" content="' . esc_url( $icons_uri . '/browserconfig.xml' ) . '">' . "\n";
		$tags .= '<meta name="theme-color" content="#ffffff">' . "\n";
		$tags .= '<meta name="apple-mobile-web-app-title" content="' . $site_name . '">' . "\n";
		$tags .= '<meta name="application-name" content="' . $site_name . '">' . "\n";

		echo $tags; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'core_wp_site_icons', 1 );


// Include components.
require get_template_directory() . '/inc/includes.php';


// Custom utility functions.
require get_template_directory() . '/inc/utility-functions.php';
