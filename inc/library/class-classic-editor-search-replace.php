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

if ( ! class_exists( 'Classic_Editor_Search_Replace' ) ) {

	class Classic_Editor_Search_Replace {
		/**
		 * Consturtor.
		 */
		public function __construct() {
		}

		/**
		 * Initializor.
		 */
		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		/**
		 * Plugin loaded.
		 */
		public function on_loaded() {
			add_filter( 'add_meta_boxes', array( $this, 'add_classic_editor_support' ) );
		}

		/**
		 * Add Classic Editor search and replace meta box.
		 */
		public function add_classic_editor_support() {
			global $post;

			$besnr_admin = new BESNR_Admin();
			$besnr       = new Block_Editor_Search_Replace();

			$has_user_cap = $besnr_admin->check_user_cap();

			if ( ! $has_user_cap ) {
				return;
			}

			// Ensure that $post is a valid WP_Post object
			if ( ! isset( $post ) || ! is_a( $post, 'WP_Post' ) ) {
				return false;
			}

			// Create an array with enabled screens to show the Navigato Controls.
			$enabled_screens = array();

			// Get supported post types and editor support options
			$post_types = $besnr->types_supported;
			$editors    = $besnr->editors_supported;

			// Verify that the Classic Editor is active
			if ( ! $this->is_classic_editor_active() ) {
				return false;
			}

			// Check for Classic Editor via GET parameters
			if ( ! array_key_exists( 'classic-editor', $_GET ) ) {
				return false;
			}

			// Available for Posts, Pages, WooCommerce Products & Custom Post Types.
			foreach ( $post_types as $post_type ) {
				array_push( $enabled_screens, $post_type );
			}

			// Check if the current post type is supported and Classic Editor is enabled
			if ( in_array( $post->post_type, $post_types, true )
				&& in_array( 'classic', $editors, true ) ) {
					add_meta_box(
						'classic_search_replace_metabox',
						esc_html__( 'Block Editor S/R', 'block-editor-search-replace' ),
						array( $this, 'display_classic_editor_meta_box' ),
						$enabled_screens,
						'side',
						'high'
					);
			}
		}

		/**
		 * Display Classic Editor meta box.
		 */
		public function display_classic_editor_meta_box() {
			global $post;

			$besnr        = new Block_Editor_Search_Replace();
			$allowed_html = BESNR_PLUGIN_ALLOWED_HTML_ARR;

			require_once BESNR_PLUGIN_DIR_PATH . 'inc/admin/views/classic-editor-meta-box.php';
		}

		/**
		 * Check if the Classic Editor is installed and active or not.
		 */
		public function is_classic_editor_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
				return true;
			}

			return false;
		}
	}

	$besnr_classic = new Classic_Editor_Search_Replace();
	$besnr_classic->init();
}
