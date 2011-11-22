<?

/*
Plugin Name: Post Thumbnail Fallback
Plugin URI: https://github.com/benhuson/post-thumbnail-fallback
Description: If no post thumbnail found, use the first post image if there is one.
Author: Ben Huson
Version: 1.0
*/

/*
Copyright 2011 Ben Huson

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

class PTFallback {
	
	/**
	 * Constructor
	 */
	function PTFallback() {
		add_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ), 10, 4 );
	}
	
	/**
	 * Get Post Metadata
	 */
	function get_post_metadata( $value, $object_id, $meta_key, $single ) {
		if ( '_thumbnail_id' == $meta_key ) {
			remove_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ) );
			$id = get_post_thumbnail_id( $object_id );
			add_filter( 'get_post_metadata', array( $this, 'get_post_metadata' ), 10, 4 );
			if ( !$id ) {
				$value = $this->get_first_image_id( $object_id );
			}
		}
		return $value;
	}
	
	/**
	 * Get first image ID
	 */
	function get_first_image_id( $post_id ) {				
		$args = array(
			'numberposts'    => 1,
			'order'          => 'ASC',
			'post_mime_type' => 'image',
			'post_parent'    => $post_id,
			'post_status'    => null,
			'post_type'      => 'attachment'
		);
		$args = apply_filters( 'ptfallback_first_image_args', $args, $post_id );
		$attachments = get_children( $args );
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				return $attachment->ID;
			}
		}
		return null;
	}
	
}

global $PTFallback;
$PTFallback = new PTFallback();


?>