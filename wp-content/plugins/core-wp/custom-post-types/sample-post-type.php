<?php
/**
 * Set up custom post types for project here.
 *
 * Replace any sample text and filenames with appropriate names that best fit
 * your project.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resources
 *
 * @link https://www.smashingmagazine.com/2012/01/create-custom-taxonomies-wordpress/
 * @link https://codex.wordpress.org/Function_Reference/register_taxonomy
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
*/

if ( ! function_exists( 'core_wp_custom_post_types' ) ) {
	/**
	 * Fires after WordPress has finished loading but before any headers are sent.
	 *
	 * @link: https://developer.wordpress.org/reference/hooks/init/
	 */
	function core_wp_custom_post_types() {
		$sample_post_type_labels = array(
			'name'                  => _x( 'Sample Posts', 'Sample Post General Name', 'core_wp' ),
			'singular_name'         => _x( 'Sample Post', 'Sample Post Singular Name', 'core_wp' ),
			'menu_name'             => __( 'Sample Posts', 'core_wp' ),
			'name_admin_bar'        => __( 'Sample Post', 'core_wp' ),
			'archives'              => __( 'Item Archives', 'core_wp' ),
			'attributes'            => __( 'Item Attributes', 'core_wp' ),
			'parent_item_colon'     => __( 'Parent Item:', 'core_wp' ),
			'all_items'             => __( 'All Items', 'core_wp' ),
			'add_new_item'          => __( 'Add New Item', 'core_wp' ),
			'add_new'               => __( 'Add New', 'core_wp' ),
			'new_item'              => __( 'New Item', 'core_wp' ),
			'edit_item'             => __( 'Edit Item', 'core_wp' ),
			'update_item'           => __( 'Update Item', 'core_wp' ),
			'view_item'             => __( 'View Item', 'core_wp' ),
			'view_items'            => __( 'View Items', 'core_wp' ),
			'search_items'          => __( 'Search Item', 'core_wp' ),
			'not_found'             => __( 'Not found', 'core_wp' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'core_wp' ),
			'featured_image'        => __( 'Featured Image', 'core_wp' ),
			'set_featured_image'    => __( 'Set featured image', 'core_wp' ),
			'remove_featured_image' => __( 'Remove featured image', 'core_wp' ),
			'use_featured_image'    => __( 'Use as featured image', 'core_wp' ),
			'insert_into_item'      => __( 'Insert into item', 'core_wp' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'core_wp' ),
			'items_list'            => __( 'Items list', 'core_wp' ),
			'items_list_navigation' => __( 'Items list navigation', 'core_wp' ),
			'filter_items_list'     => __( 'Filter items list', 'core_wp' ),
		);

		$sample_post_type_args = array(
			'label'               => __( 'Sample Post', 'core_wp' ),
			'description'         => __( 'Sample Post Description', 'core_wp' ),
			'labels'              => $sample_post_type_labels,
			'show_in_rest'        => true,
			// Features this CPT supports in Post Editor.
			'supports'            => array( 'title', 'revisions' ),
			/**
			 * A hierarchical CPT is like Pages and can have
			 * Parent and child items. A non-hierarchical CPT
			 * is like Posts.
			 */
			'hierarchical'        => false,
			// category, post_tag.
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'menu_icon'           => 'dashicons-admin-post',
			'rewrite'             => array(
				'slug'       => '',
				'with_front' => false,
			),
		);

		register_post_type( 'sample_post', $sample_post_type_args );
	}
}
add_action( 'init', 'core_wp_custom_post_types', 0 );
