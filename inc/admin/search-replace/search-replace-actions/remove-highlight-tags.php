<?php
/**
 * [Short description]
 *
 * @package    DEVRY\BESNR
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.2
 */

namespace DEVRY\BESNR;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Remove the custom tags used for highlighting.
 */
function besnr_remove_highlight_tags( $post_id = '', $is_ajax = true ) {
	$besnr       = new Block_Editor_Search_Replace();
	$besnr_admin = new BESNR_Admin();

	$besnr_admin->get_invalid_nonce_token();
	$besnr_admin->get_invalid_user_cap();

	if ( ! $post_id ) {
		$post_id = isset( $_REQUEST['current_post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['current_post_id'] ) ) ) : 0;
	}

	if ( ! $post_id ) {
		$besnr_admin->print_json_message(
			0,
			__( 'Post ID not found!', 'block-editor-search-replace' )
		);
	}

	$post         = get_post( $post_id );
	$post_content = $post->post_content;

	if ( empty( $post_content ) ) {
		$besnr_admin->print_json_message(
			0,
			__( 'No content found for this post ID.', 'block-editor-search-replace' )
		);
	}

	// Use DOMDocument to remove all <besnr-replace>, <besnr-highlight> tags including nested ones
	$dom = new \DOMDocument();

	libxml_use_internal_errors( true ); // Suppress HTML warnings

	$dom->loadHTML(
		mb_convert_encoding( $post_content, 'HTML-ENTITIES', 'UTF-8' ),
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);

	libxml_clear_errors();

	// Remove all <besnr-replace> elements
	$xpath = new \DOMXPath( $dom );
	$nodes = $xpath->query( '//besnr-replace' );

	foreach ( $nodes as $node ) {
		while ( $node->hasChildNodes() ) {
			$node->parentNode->insertBefore( $node->firstChild, $node ); // phpcs:ignore
		}
		$node->parentNode->removeChild( $node ); // phpcs:ignore
	}

	// Remove all <besnr-highlight> elements
	$nodes = $xpath->query( '//besnr-highlight' );

	foreach ( $nodes as $node ) {
		while ( $node->hasChildNodes() ) {
			$node->parentNode->insertBefore( $node->firstChild, $node ); // phpcs:ignore
		}
		$node->parentNode->removeChild( $node ); // phpcs:ignore
	}

	// Save the cleaned content.
	$post_content_cleaned = $dom->saveHTML();

	if ( $besnr->update_post_content( $post, $post_content_cleaned ) ) {
		// Used when the function directly don't need to exit with JSON response.
		if ( ! $is_ajax ) {
			return $post_content_cleaned;
		}

		$besnr_admin->print_json_message(
			1,
			__( 'All custom tags have been removed successfully!', 'block-editor-search-replace' )
		);
	} else {
		$besnr_admin->print_json_message(
			0,
			__( 'Unexpected error! Post content was not updated!', 'block-editor-search-replace' )
		);
	}
}

add_action( 'wp_ajax_besnr_remove_highlight_tags', __NAMESPACE__ . '\besnr_remove_highlight_tags' );
