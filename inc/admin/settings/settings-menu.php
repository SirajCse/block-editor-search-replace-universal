<?php
/**
 * [Short description]
 *
 * @package    DEVRY\BESNR
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BESNR;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

function besnr_add_settings_menu() {
	$besnr = new Block_Editor_Search_Replace();

	if ( '' === $besnr->compact_mode ) {
		add_menu_page(
			esc_html__( 'Block Editor Search & Replace', 'block-editor-search-replace' ),
			esc_html__( 'Blocks Editor S/R', 'block-editor-search-replace' ),
			'manage_options',
			BESNR_SETTINGS_SLUG,
			__NAMESPACE__ . '\besnr_display_settings_page',
			'dashicons-search',
			79.998
		);
	} else {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Block Editor Search & Replace', 'block-editor-search-replace' ),
			esc_html__( 'Search & Replace', 'block-editor-search-replace' ),
			'manage_options',
			BESNR_SETTINGS_SLUG,
			__NAMESPACE__ . '\besnr_display_settings_page'
		);
	}
}

add_action( 'admin_menu', __NAMESPACE__ . '\besnr_add_settings_menu', 1000 );
