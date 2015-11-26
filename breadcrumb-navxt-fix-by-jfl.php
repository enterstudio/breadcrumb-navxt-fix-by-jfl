<?php
/*
Plugin Name: Breadcrumb NavXT Fix by JFL
Plugin URI: http://github.com/jflagarde/breadcrumb-navxt-fix-by-jfl/
Description: Fix the Breadcrumb NavXT plugin to work with custom post types such as the ones used in The Events Calendar plugins. Before this fix, the breadcrumb doesn't work for an event page. Also fix the home page title
Version: 1.0.0
Author: Jean-Francois Lagarde
Author URI: http://jflagarde.com/
License: GPL2
Text Domain: breadcrumb-navxt-fix-by-jfl
Domain Path: /languages
*/
/*  Copyright 2015  Jean-Francois Lagarde  (email : jeanfrancoislagarde@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// This PHP file can't be called outside of WordPress
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Include the fixed breadcrumb_trail class
require_once( plugin_dir_path( __FILE__ ) . 'class.fixed_bcn_breadcrumb_trail.php' );

/**
 * Filter the titles of breadcrumbs. If it's the home page, set it's title ("Home") instead of using the site's title
 *
 * @param $title
 * @param $type
 * @param $id
 *
 * @return string
 */
function filter_bcn_breadcrumb_title( $title, $type, $id ) {

	// If it's the home page
	if ( 'home' === $type[0] ) {

		// Use the page's title ("Home") instead of using the site's title
		$title = get_the_title( get_option( 'page_on_front' ) );

	}

	return $title;

}
add_filter( 'bcn_breadcrumb_title', 'filter_bcn_breadcrumb_title', 42, 3 );

/**
 * Filter the breadcrumb trail class and use the fixed one
 *
 * @param $bcn_breadcrumb_trail
 *
 * @return fixed_bcn_breadcrumb_trail
 */
function filter_bcn_breadcrumb_trail_object( $bcn_breadcrumb_trail ) {

	// Use the fixed breadcrumb_trail class
	$bcn_breadcrumb_trail = new fixed_bcn_breadcrumb_trail();

	return $bcn_breadcrumb_trail;

}
add_filter( 'bcn_breadcrumb_trail_object', 'filter_bcn_breadcrumb_trail_object', 10, 1 );