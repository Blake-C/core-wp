<?php
/**
 * Plugin Name:  Core WP
 * Description:  Project custom features and functionality.
 * Plugin URI:
 * Version:      0.0.0
 * Author:
 * Author URI:
 * Text Domain:  core_wp
 * License:      GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Core WP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Softwatwitchre Foundation, either version 2 of the License, or
 * any later version.
 *
 * Core WP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Core WP. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CORE_WP_FILES' ) ) {
	define( 'CORE_WP_FILES', __FILE__ );
}

require_once dirname( CORE_WP_FILES ) . '/custom-post-types/sample-post-type.php';
