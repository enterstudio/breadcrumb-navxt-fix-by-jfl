<?php
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

// Include the original breadcrumb_trail class to be able to extend our new and modified class
require_once( ABSPATH . 'wp-content/plugins/breadcrumb-navxt/class.bcn_breadcrumb_trail.php' );

/**
 * The modified breadcrumb trail class
 */
class fixed_bcn_breadcrumb_trail extends bcn_breadcrumb_trail {

	/**
	 * The modified Breadcrumb Trail Filling Function
	 * Only one line was added:
	 * wp_reset_postdata();
	 * before
	 * $this->do_post( $GLOBALS['post'] );
	 */
	public function fill() {
		global $wpdb, $wp_query;
		//Check to see if the trail is already populated
		if ( count( $this->breadcrumbs ) > 0 ) {
			//Exit early since we have breadcrumbs in the trail
			return null;
		}
		//Do any actions if necessary, we past through the current object instance to keep life simple
		do_action( 'bcn_before_fill', $this );
		//Do specific opperations for the various page types
		//Check if this isn't the first of a multi paged item
		if ( $this->opt['bpaged_display'] && ( is_paged() || is_singular() && get_query_var( 'page' ) > 1 ) ) {
			$this->do_paged();
		}
		//For the front page, as it may also validate as a page, do it first
		if ( is_front_page() ) {
			//Must have two seperate branches so that we don't evaluate it as a page
			if ( $this->opt['bhome_display'] ) {
				$this->do_front_page();
			}
		} //For posts
		else if ( is_singular() ) {
			//For attachments
			if ( is_attachment() ) {
				$this->do_attachment();
			} //For all other post types
			else {
				/*
				 * The following line is the fix for custom post types like the events
				 */
				wp_reset_postdata();

				$this->do_post( $GLOBALS['post'] );
			}
		} //For searches
		else if ( is_search() ) {
			$this->do_search();
		} //For author pages
		else if ( is_author() ) {
			$this->do_author();
		} //For archives
		else if ( is_archive() ) {
			$type = $wp_query->get_queried_object();
			//For date based archives
			if ( is_date() ) {
				$this->do_archive_by_date( $this->get_type_string_query_var() );
				$this->type_archive( $type );
			} //If we have a post type archive, and it does not have a root page generate the archive
			else if ( is_post_type_archive() && ! isset( $type->taxonomy )
			          && ( ! is_numeric( $this->opt[ 'apost_' . $type->name . '_root' ] ) || $this->opt[ 'bpost_' . $type->name . '_archive_display' ] )
			) {
				$this->do_archive_by_post_type();
			} //For taxonomy based archives
			else if ( is_category() || is_tag() || is_tax() ) {
				$this->do_archive_by_term();
				$this->type_archive( $type );
			} else {
				$this->type_archive( $type );
			}
		} //For 404 pages
		else if ( is_404() ) {
			$this->do_404();
		} else {
			//If we are here, there may have been problems detecting the type
			$type = $wp_query->get_queried_object();
			//If it looks, walks, and quacks like a taxonomy, treat is as one
			if ( isset( $type->taxonomy ) ) {
				$this->do_archive_by_term();
				$this->type_archive( $type );
			}
		}
		//We always do the home link last, unless on the frontpage
		if ( ! is_front_page() ) {
			$this->do_root();
			$this->do_home();
		}
		//Do any actions if necessary, we past through the current object instance to keep life simple
		do_action( 'bcn_after_fill', $this );
	}
}