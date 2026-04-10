<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package core_wp
 */


/**
 * Classes
 *
 * Ex:
 * require get_template_directory() . '/inc/classes/name-of-class-file.php';
 */


/**
 * Components
 */
require get_template_directory() . '/inc/components/body-classes.php';
require get_template_directory() . '/inc/components/embed-video-container.php';
require get_template_directory() . '/inc/components/excerpt-length.php';
require get_template_directory() . '/inc/components/excerpt-more.php';
require get_template_directory() . '/inc/components/gform-filters.php';
require get_template_directory() . '/inc/components/meta-description.php';
require get_template_directory() . '/inc/components/password-form.php';
require get_template_directory() . '/inc/components/site-icons.php';
require get_template_directory() . '/inc/components/skip-link-tabindex.php';
require get_template_directory() . '/inc/components/thumbnail-upscale.php';


/**
 * Blocks
 */
require get_template_directory() . '/inc/blocks/related-posts/block.php';
require get_template_directory() . '/inc/blocks/social-share/block.php';


/**
 * Template Tags
 */
require get_template_directory() . '/inc/template-tags/single-post.php';


/**
 * Customize the admin sceen
 */
require get_template_directory() . '/inc/custom-admin-functions/change-howdy-text.php';
require get_template_directory() . '/inc/custom-admin-functions/login-page-favicon.php';
require get_template_directory() . '/inc/custom-admin-functions/login-page-logo-title-tag.php';
require get_template_directory() . '/inc/custom-admin-functions/login-page-logo-url.php';
require get_template_directory() . '/inc/custom-admin-functions/login-page-styles.php';


/**
 * Shortcodes
 */
require get_template_directory() . '/inc/shortcodes/copyright.php';
