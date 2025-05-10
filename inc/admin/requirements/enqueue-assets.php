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

/**
 * Enqueue admin assets (styles and scripts) for the plugin.
 */
function besnr_enqueue_admin_assets() {
	if ( ! is_admin() ) {
		return;
	}

	$besnr_admin = new BESNR_Admin();
	$besnr       = new Block_Editor_Search_Replace();

	$has_user_cap   = $besnr_admin->check_user_cap();
	$current_screen = get_current_screen();

	wp_enqueue_style(
		'besnr-admin',
		BESNR_PLUGIN_DIR_URL . 'assets/dist/css/besnr-admin.min.css',
		array(),
		BESNR_PLUGIN_VERSION,
		'all'
	);

	// Load assets only for page page staring with prefix besnr-.
	if ( strpos( $current_screen->id, 'besnr_' ) ) {
		wp_enqueue_script(
			'besnr-admin',
			BESNR_PLUGIN_DIR_URL . 'assets/dist/js/besnr-admin.min.js',
			array( 'jquery' ),
			BESNR_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'besnr-admin',
			'besnr',
			array(
				'plugin_url'    => BESNR_PLUGIN_DIR_URL,
				'plugin_domain' => BESNR_PLUGIN_DOMAIN,
				'ajax_url'      => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajax_nonce'    => wp_create_nonce( 'besnr_ajax_nonce' ),
			)
		);
	}

	if ( ! $has_user_cap ) {
		return;
	}

	if ( ! array_intersect( wp_get_current_user()->roles, $besnr->user_access ) ) { // Has user access.
		return;
	}

	if ( ! in_array( $current_screen->post_type, $besnr->types_supported, true ) ) { // // Post type supported.
		return;
	}

	// Enqueue assets if classic editor is supported and loaded.
	if ( in_array( 'classic', $besnr->editors_supported, true )
		&& array_key_exists( 'classic-editor', $_GET ) ) {
		wp_enqueue_style(
			'besnr-classic-editor',
			BESNR_PLUGIN_DIR_URL . 'assets/dist/css/besnr-editors.min.css',
			array(),
			BESNR_PLUGIN_VERSION,
			'all'
		);

		wp_enqueue_script(
			'besnr-classic-editor',
			BESNR_PLUGIN_DIR_URL . 'assets/dist/js/besnr-classic-editor.min.js',
			array( 'jquery' ),
			BESNR_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'besnr-classic-editor',
			'besnr',
			array(
				'plugin_url'    => BESNR_PLUGIN_DIR_URL,
				'plugin_domain' => BESNR_PLUGIN_DOMAIN,
				'ajax_url'      => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajax_nonce'    => wp_create_nonce( 'besnr_ajax_nonce' ),
			)
		);
	}
}

/**
 * Enqueue block editor assets below.
 */
function besnr_enqueue_block_editor_assets() {
	if ( ! is_admin() ) {
		return;
	}

	$besnr_admin = new BESNR_Admin();
	$besnr       = new Block_Editor_Search_Replace();

	$has_user_cap = $besnr_admin->check_user_cap();

	$current_screen = get_current_screen();

	if ( ! $has_user_cap ) {
		return;
	}

	if ( ! array_intersect( wp_get_current_user()->roles, $besnr->user_access ) ) { // Has user access.
		return;
	}

	if ( ! in_array( $current_screen->post_type, $besnr->types_supported, true ) ) { // // Post type supported.
		return;
	}

	// Enqueue assets if block editor is supported.
	if ( in_array( 'block', $besnr->editors_supported, true ) ) {
		wp_enqueue_style(
			'besnr-block-editor',
			BESNR_PLUGIN_DIR_URL . 'assets/dist/css/besnr-editors.min.css',
			array(),
			BESNR_PLUGIN_VERSION,
			'all'
		);

		wp_enqueue_script(
			'besnr-block-editor',
			BESNR_PLUGIN_DIR_URL . 'assets/dist/js/besnr-block-editor.min.js',
			array( 'wp-blocks', 'wp-element', 'wp-plugins', 'wp-edit-post', 'wp-data' ), // Dependencies.
			BESNR_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'besnr-block-editor',
			'besnr',
			array(
				'plugin_url'    => BESNR_PLUGIN_DIR_URL,
				'plugin_domain' => BESNR_PLUGIN_DOMAIN,
				'ajax_url'      => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajax_nonce'    => wp_create_nonce( 'besnr_ajax_nonce' ),
			)
		);
	}
}

add_action( 'enqueue_block_assets', __NAMESPACE__ . '\besnr_enqueue_block_editor_assets' );
