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
 * [AJAX] Update editor contents and reload with new contents.
 */
function besnr_update_editor_contents() {
	$besnr_admin = new BESNR_Admin();

	$besnr_admin->get_invalid_nonce_token();
	$besnr_admin->get_invalid_user_cap();

	$current_post_id = isset( $_REQUEST['current_post_id'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['current_post_id'] ) ) ) : 0;

	if ( ! $current_post_id ) {
		$besnr_admin->print_json_message(
			0,
			__( 'Post ID not found!', 'block-editor-search-replace' )
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

	echo wp_json_encode(
		array(
			array(
				'status'  => 2,
				'message' => htmlspecialchars( $post_content ),
			),
		)
	);

	exit;
}

add_action( 'wp_ajax_besnr_update_editor_contents', __NAMESPACE__ . '\besnr_update_editor_contents' );
