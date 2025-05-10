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
 * [AJAX] Replace content based on search method.
 */
function besnr_default_search_replace() {
	global $wpdb;

	$besnr       = new Block_Editor_Search_Replace();
	$besnr_admin = new BESNR_Admin();

	$besnr_admin->get_invalid_nonce_token();
	$besnr_admin->get_invalid_user_cap();

	$current_post_id   = isset( $_REQUEST['current_post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['current_post_id'] ) ) ) : 0;
	$search_input      = isset( $_REQUEST['search_input'] ) ? trim( wp_unslash( $_REQUEST['search_input'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized	
	$replace_input     = isset( $_REQUEST['replace_with_input'] ) ? trim( wp_unslash( $_REQUEST['replace_with_input'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$search_method     = isset( $_REQUEST['search_method'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['search_method'] ) ) ) : '';
	$is_highlighted    = isset( $_REQUEST['is_highlighted'] ) ? filter_var( sanitize_text_field( wp_unslash( $_REQUEST['is_highlighted'] ) ), FILTER_VALIDATE_BOOLEAN ) : false;
	$is_case_sensitive = isset( $_REQUEST['is_case_sensitive'] ) ? filter_var( sanitize_text_field( wp_unslash( $_REQUEST['is_case_sensitive'] ) ), FILTER_VALIDATE_BOOLEAN ) : false;

	if ( ! $current_post_id ) {
		$besnr_admin->print_json_message(
			0,
			__( 'Post ID not found!', 'block-editor-search-replace' )
		);
	}

	if ( empty( $search_input ) ) {
		$besnr_admin->print_json_message(
			0,
			__( 'No search phrase provided!', 'block-editor-search-replace' )
		);
	}

	if ( empty( $replace_input ) ) {
		$besnr_admin->print_json_message(
			0,
			__( 'No replace with phrase provided!', 'block-editor-search-replace' )
		);
	}

	$post         = get_post( $current_post_id );
	$post_content = $post->post_content;

	if ( empty( $post_content ) ) {
		$besnr_admin->print_json_message(
			0,
			__( 'No content found for this post ID.', 'block-editor-search-replace' )
		);
	}

	if ( 'text' === $search_method ) {
		$post_content_replaced = $besnr->replace_text_only( $post_content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive );
	} elseif ( 'url' === $search_method ) {
		$post_content_replaced = $besnr->replace_urls( $post_content, $search_input, $replace_input, $is_highlighted );
	} elseif ( 'image' === $search_method ) {
		$post_content_replaced = $besnr->replace_images( $post_content, $search_input, $replace_input, $is_highlighted );
	} elseif ( 'multiple' === $search_method ) {
		// Convert comma-separated phrases into arrays
		$search_terms  = array_map( 'trim', explode( ',', $search_input ) );
		$replace_terms = array_map( 'trim', explode( ',', $replace_input ) );

		// Validate that both arrays have the same number of terms
		if ( count( $search_terms ) !== count( $replace_terms ) ) {
			$besnr_admin->print_json_message(
				0,
				__( 'The number of search terms does not match the number of replace terms.', 'block-editor-search-replace' )
			);
		}

		$post_content_replaced = $besnr->replace_multiple_terms( $post_content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive );
	} else {
		$besnr_admin->print_json_message(
			0,
			__( 'Unsupported search method!', 'block-editor-search-replace' )
		);
	}

	// Backup the original post content without custom tags; we will have at least 1 back at all times even in 'none'.
	$count_occurrences = $besnr->count_occurrences( $post_content, $search_input, $is_highlighted, $is_case_sensitive );

	if ( $besnr->update_post_content( $post, $post_content_replaced ) ) {
		$besnr_admin->print_json_message(
			1,
			sprintf(
				/* translators: %d is replace with "# occurrence(s)" */
				__( '%s have been replaced successfully!', 'block-editor-search-replace' ),
				'<strong>' . $count_occurrences . ' occurrence(s)</strong>'
			)
		);
	} else {
		$besnr_admin->print_json_message(
			0,
			__( 'Unexpected error! Post content was not updated!', 'block-editor-search-replace' )
		);
	}
}

add_action( 'wp_ajax_besnr_default_search_replace', __NAMESPACE__ . '\besnr_default_search_replace' );
