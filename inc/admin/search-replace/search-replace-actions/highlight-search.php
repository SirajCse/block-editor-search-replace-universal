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
 * [AJAX] Highlight searched text on each keystroke.
 */
function besnr_highlight_search() {
	$besnr       = new Block_Editor_Search_Replace();
	$besnr_admin = new BESNR_Admin();

	$besnr_admin->get_invalid_nonce_token();
	$besnr_admin->get_invalid_user_cap();

	$current_post_id   = isset( $_REQUEST['current_post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['current_post_id'] ) ) ) : 0;
	$search_input      = isset( $_REQUEST['search_input'] ) ? trim( wp_unslash( $_REQUEST['search_input'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$search_method     = isset( $_REQUEST['search_method'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['search_method'] ) ) ) : '';
	$is_case_sensitive = isset( $_REQUEST['is_case_sensitive'] ) ? filter_var( sanitize_text_field( wp_unslash( $_REQUEST['is_case_sensitive'] ) ), FILTER_VALIDATE_BOOLEAN ) : false;
	$is_highlighted    = isset( $_REQUEST['is_highlighted'] ) ? filter_var( sanitize_text_field( wp_unslash( $_REQUEST['is_highlighted'] ) ), FILTER_VALIDATE_BOOLEAN ) : false;

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

	// Remove all existing highlight tags, call the AJAX func directly.
	besnr_remove_highlight_tags( $current_post_id, false );

	$post         = get_post( $current_post_id );
	$post_content = $post->post_content;

	if ( empty( $post_content ) ) {
		$besnr_admin->print_json_message(
			0,
			__( 'No content found for this post ID.', 'block-editor-search-replace' )
		);
	}

	if ( 'text' === $search_method ) {
		$post_content_highlighted = $besnr->highlight_text_only( $post_content, $search_input, $is_highlighted, $is_case_sensitive );
	} elseif ( 'url' === $search_method ) {
		$post_content_highlighted = $besnr->highlight_urls( $post_content, $search_input, $is_highlighted );
	} elseif ( 'image' === $search_method ) {
		$post_content_highlighted = $besnr->highlight_images( $post_content, $search_input, $is_highlighted );
	} elseif ( 'multiple' === $search_method ) {
		$post_content_highlighted = $besnr->highlight_multiple_terms( $post_content, $search_input, $is_highlighted, $is_case_sensitive );
	} else {
		$besnr_admin->print_json_message(
			0,
			__( 'Unsupported search method!', 'block-editor-search-replace' )
		);
	}

	// Normalize custom tags.
	$post_content_highlighted = str_replace(
		array( '&lt;besnr-replace&gt;', '&lt;/besnr-replace&gt;', '&lt;besnr-highlight&gt;', '&lt;/besnr-highlight&gt;' ),
		array( '<besnr-replace>', '</besnr-replace>', '<besnr-highlight>', '</besnr-highlight>' ),
		$post_content_highlighted
	);

	$count_occurrences = $besnr->count_occurrences( $post_content_highlighted, $search_input, $is_highlighted, $is_case_sensitive );

	if ( $besnr->update_post_content( $post, $post_content_highlighted ) ) {
		$besnr_admin->print_json_message(
			2,
			sprintf(
				/* translators: %s is replace with "# occurrence(s)" */
				__( '%s have been found! ', 'block-editor-search-replace' ),
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

add_action( 'wp_ajax_besnr_highlight_search', __NAMESPACE__ . '\besnr_highlight_search' );
