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

! defined( ABSPATH ) || exit; // Exit if accessed directly

/**
 * Don't allow to have both Free and Pro active at the same time.
 */
function besnr_check_pro_plugin() {
	// Deactitve the Pro version if active.
	if ( is_plugin_active( 'block-editor-search-replace-pro/block-editor-search-replace.php' ) ) {
		deactivate_plugins( 'block-editor-search-replace-pro/block-editor-search-replace.php', true );
	}
}

register_activation_hook( BESNR_PLUGIN_BASENAME, __NAMESPACE__ . '\besnr_check_pro_plugin' );

/**
 * Display a promotion for the pro plugin.
 */
function besnr_display_upgrade_notice() {
	$besnr_admin = new BESNR_Admin();

	if ( get_option( 'besnr_upgrade_notice' ) && get_transient( 'besnr_upgrade_plugin' ) ) {
		return;
	}
	?>
		<div class="notice notice-success is-dismissible besnr-admin">
			<!-- <p class="besnr-upgrade-notice-discount"> -->
				<?php
					// printf(
					// 	wp_kses(
					// 		/* translators: %1$s is replaced with promo code */
					// 		/* translators: %2$s is replaced with 10% off */
					// 		__( 'Use the %1$s code and get %2$s your purchase!', 'block-editor-search-replace' ),
					// 		json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true )
					// 	),
					// 	'<code>' . esc_html__( 'BESNR10', 'block-editor-search-replace' ) . '</code>',
					// 	'<strong>' . esc_html__( '10% off', 'block-editor-search-replace' ) . '</strong>'
					// );
				?>
			<!-- </p> -->
			<h3>
				<?php echo esc_html__( 'Block Editor Search & Replace PRO 🚀', 'block-editor-search-replace' ); ?>
			</h3>
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %1$s is replaced with Found the free version helpful */
						/* translators: %2$s is replaced with Block Editor Search & Replace */
						__( '✨🎉📢 %1$s? Would you be interested in learning more about the benefits of upgrading to the %2$s?', 'block-editor-search-replace' ),
						json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true )
					),
					'<strong>' . esc_html__( 'Found the free version helpful', 'block-editor-search-replace' ) . '</strong>',
					'<strong>' . esc_html__( 'Block Editor Search & Replace', 'block-editor-search-replace' ) . '</strong>'
				);
				?>
			</p>
			<div class="button-group">
				<a href="https://bit.ly/4alPawW" target="_blank" class="button button-primary button-success">
					<?php echo esc_html__( 'Go Pro', 'block-editor-search-replace' ); ?>
					<i class="dashicons dashicons-external"></i>
				</a>
				<a href="<?php echo esc_url( admin_url( $besnr_admin->admin_page . BESNR_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'besnr_upgrade_notice_nonce' ) . '&action=besnr_dismiss_upgrade_notice' ) ); ?>" class="button">
					<?php echo esc_html__( 'I already did', 'block-editor-search-replace' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( $besnr_admin->admin_page . BESNR_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'besnr_upgrade_notice_nonce' ) . '&action=besnr_dismiss_upgrade_notice' ) ); ?>" class="button">
					<?php echo esc_html__( "Don't show this notice again!", 'block-editor-search-replace' ); ?>
				</a>
			</div>
		</div>
	<?php
	delete_option( 'besnr_upgrade_notice' );

	// Set the transient to last for 30 days.
	set_transient( 'besnr_upgrade_plugin', true, 30 * DAY_IN_SECONDS );
}

add_action( 'admin_notices', __NAMESPACE__ . '\besnr_display_upgrade_notice' );
