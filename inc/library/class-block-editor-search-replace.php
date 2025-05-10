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

if ( ! class_exists( 'Block_Editor_Search_Replace' ) ) {

	class Block_Editor_Search_Replace {
		// Supported post types.
		public $types_supported;

		// Editor types.
		public $editors_supported;

		// User access.
		public $user_access;

		// Compact mode.
		public $compact_mode;

		/**
		 * Consturtor.
		 */
		public function __construct() {
			// Use some defaults for the Options, for initial plugin usage.
			$this->types_supported   = array( 'page', 'post' );
			$this->editors_supported = array( 'block', 'classic' );
			$this->user_access       = array( 'administrator' );
			$this->compact_mode      = ''; // No

			// Retrieve from options, if available; otherwise, use the default values.
			$this->types_supported   = get_option( 'besnr_pro_types_supported', $this->types_supported );
			$this->editors_supported = get_option( 'besnr_pro_editors_supported', $this->editors_supported );
			$this->user_access       = get_option( 'besnr_pro_user_access', $this->user_access );
			$this->compact_mode      = get_option( 'besnr_pro_compact_mode', $this->compact_mode );
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
		}

		/**
		 * Update post content with new after search and replace action.
		 */
		public function update_post_content( $post, $post_content_updated ) {
			if ( is_a( $post, 'WP_Post' ) ) {
				// Temporarily remove the post revision saving filter
				remove_filter( 'post_updated', 'wp_save_post_revision' );

				// Convert to HTML entities for non-ASCII characters.
				$post_content_updated = mb_encode_numericentity(
					$post_content_updated,
					array( 0x80, 0x10FFFF, 0, 0x10FFFF ), // Define a conversion map for non-ASCII characters.
					'UTF-8'
				);

				// Update the post and check for errors
				$result = wp_update_post(
					array(
						'ID'           => $post->ID,
						'post_content' => preg_replace( '/\R/', '', $post_content_updated ),
					),
					true
				);

				if ( is_wp_error( $result ) ) {
					return false;
				}

				// Re-add the post revision saving filter
				add_filter( 'post_updated', 'wp_save_post_revision' );

				return true;
			}

			return false;
		}

		/**
		 * Count the search and replace occurances.
		 */
		public function count_occurrences( $content, $search_replace_input, $is_highlighted, $is_case_sensitive ) {
			// Check if the input contains multiple terms separated by commas.
			$search_terms = strpos( $search_replace_input, ',' ) !== false
				? array_map( 'trim', explode( ',', $search_replace_input ) ) // Split into multiple terms.
				: array( $search_replace_input ); // Treat as a single term.

			$modifiers   = $is_case_sensitive ? '' : 'i';
			$total_count = 0;

			foreach ( $search_terms as $term ) {
				// Handle raw URLs or terms inside <besnr-highlight>/<besnr-replace>.
				$search_replace_pattern = $is_highlighted
					? '/<besnr-highlight\b[^>]*>' . preg_quote( $term, '/' ) . '<\/besnr-highlight>/' . $modifiers
					: '/<besnr-replace\b[^>]*>' . preg_quote( $term, '/' ) . '<\/besnr-replace>/' . $modifiers;

				// Count matches for raw URLs or highlighted/replaced terms.
				preg_match_all( $search_replace_pattern, $content, $matches );
				$total_count += count( $matches[0] );

				// Handle `<a>` tags with href containing the search term.
				$link_pattern = '/<a\b[^>]*href=[\'"]' . preg_quote( $term, '/' ) . '[\'"][^>]*>.*?<\/a>/is';
				preg_match_all( $link_pattern, $content, $link_matches );
				$total_count += count( $link_matches[0] );

				// Handle `<img>` tags with src containing the search term.
				$img_pattern = '/<img\b[^>]*src=[\'"]' . preg_quote( $term, '/' ) . '[\'"][^>]*>/is';
				preg_match_all( $img_pattern, $content, $img_matches );
				$total_count += count( $img_matches[0] );
			}

			return $total_count; // Return the total number of matches.
		}


		/**
		 * Highlight (plain) text-only and re-apply HTML structure.
		 */
		public function highlight_text_only( $html_content, $search_input, $is_highlighted, $is_case_sensitive ) {
			if ( empty( $search_input ) ) {
				return $html_content; // Return original content if search phrase is empty
			}

			// Extract plain text
			$content = $this->highlight_extract_content( $html_content );

			// Perform replacement or highlighting on plain text
			$this->highlight_case_sensitive_content( $content, $search_input, $is_highlighted, $is_case_sensitive );

			// Use DOMDocument to reapply the HTML structure
			$dom = new \DOMDocument();

			@$dom->loadHTML( // phpcs:ignore
				mb_convert_encoding( $html_content, 'HTML-ENTITIES', 'UTF-8' ),
				LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
			);

			// Replace text nodes in the DOM
			$xpath = new \DOMXPath( $dom );

			foreach ( $xpath->query( '//text()' ) as $text_node ) {
				$original_text = $text_node->nodeValue; // phpcs:ignore
				$updated_text  = $this->highlight_case_sensitive_content( $original_text, $search_input, $is_highlighted, $is_case_sensitive );

				// Only update the text node if a replacement occurred
				if ( $original_text !== $updated_text ) {
					$text_node->nodeValue = $updated_text; // phpcs:ignore
				}
			}

			// Return the updated HTML
			return $dom->saveHTML();
		}

		/**
		 * Replace highlighted text-only from the HTML content.
		 */
		public function replace_text_only( $content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive ) {
			if ( empty( $search_input ) || empty( $replace_input ) ) {
				return $content; // Return original content if any required field is empty
			}

			$modifiers = $is_case_sensitive ? '' : 'i';

			$search_pattern = ( $is_highlighted )
				? '/<besnr-highlight\b[^>]*>' . preg_quote( $search_input, '/' ) . '<\/besnr-highlight>/' . $modifiers
				: '/<besnr-replace\b[^>]*>' . preg_quote( $search_input, '/' ) . '<\/besnr-replace>/' . $modifiers;

			return preg_replace( $search_pattern, $replace_input, $content );
		}

		/**
		 * Highlight URLs from the HTML content.
		 *
		 * Tags: <a>
		 * Attributes: href
		 */
		public function highlight_urls( $html_content, $search_input, $is_highlighted ) {
			if ( empty( $search_input ) ) {
				return $html_content; // Return original content if search phrase is empty
			}

			$dom = new \DOMDocument();

			@$dom->loadHTML( // phpcs:ignore
				mb_convert_encoding( $html_content, 'HTML-ENTITIES', 'UTF-8' ),
				LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
			);

			$xpath = new \DOMXPath( $dom );

			// Target <a> tags
			$links = $xpath->query( '//a[@href]' );

			foreach ( $links as $link ) {
				$href = $link->getAttribute( 'href' );

				if ( stripos( $href, $search_input ) !== false ) {
					$wrapper = $dom->createElement( 'besnr-replace' );

					if ( $is_highlighted ) {
						$wrapper = $dom->createElement( 'besnr-highlight' );
					}

					$link->parentNode->replaceChild( $wrapper, $link ); // phpcs:ignore
					$wrapper->appendChild( $link );
				}
			}

			return $dom->saveHTML();
		}

		/**
		 * Replace highlighted URLs from the HTML content.
		 */
		public function replace_urls( $content, $search_input, $replace_input, $is_highlighted ) {
			if ( empty( $search_input ) || empty( $replace_input ) ) {
				return $content; // Return original content if any required field is empty.
			}

			// Regex to match <a> tags wrapped inside <besnr-highlight> or <besnr-replace> with specific href value.
			$search_pattern = $is_highlighted
				? '/<besnr-highlight\b[^>]*>(<a\b[^>]*href=[\'"]' . preg_quote( $search_input, '/' ) . '[\'"][^>]*>.*?<\/a>)<\/besnr-highlight>/is'
				: '/<besnr-replace\b[^>]*>(<a\b[^>]*href=[\'"]' . preg_quote( $search_input, '/' ) . '[\'"][^>]*>.*?<\/a>)<\/besnr-replace>/is';

			// Perform replacement using callback.
			return preg_replace_callback(
				$search_pattern,
				function ( $matches ) use ( $replace_input ) {
					// Extract the matched <a> tag.
					$original_tag = $matches[1];

					// Replace the `href` attribute value dynamically.
					$updated_tag = preg_replace(
						'/(href=["\'])[^"\']*(["\'])/',
						'$1' . esc_attr( $replace_input ) . '$2',
						$original_tag
					);

					// Return the updated <a> tag without the wrapping <besnr-highlight> or <besnr-replace>.
					return $updated_tag;
				},
				$content
			);
		}

		/**
		 * Highlight images from the HTML content.
		 *
		 * Tags: <img/>
		 * Attributes: src
		 */
		public function highlight_images( $html_content, $search_input, $is_highlighted ) {
			if ( empty( $search_input ) ) {
				return $html_content; // Return original content if search phrase is empty
			}

			$dom = new \DOMDocument();

			@$dom->loadHTML( // phpcs:ignore
				mb_convert_encoding( $html_content, 'HTML-ENTITIES', 'UTF-8' ),
				LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
			);

			$xpath = new \DOMXPath( $dom );

			// Target <img> tags
			$images = $xpath->query( '//img[@src]' );

			foreach ( $images as $image ) {
				$src = $image->getAttribute( 'src' );

				if ( stripos( $src, $search_input ) !== false ) {
					$wrapper = $dom->createElement( 'besnr-replace' );

					if ( $is_highlighted ) {
						$wrapper = $dom->createElement( 'besnr-highlight' );
					}

					$image->parentNode->replaceChild( $wrapper, $image ); // phpcs:ignore
					$wrapper->appendChild( $image );
				}
			}

			return $dom->saveHTML();
		}

		/**
		 * Replace highlighted images from the HTML content.
		 */
		public function replace_images( $content, $search_input, $replace_input, $is_highlighted ) {
			if ( empty( $search_input ) || empty( $replace_input ) ) {
				return $content; // Return original content if any required field is empty.
			}

			// Regex to match the <besnr-highlight> or <besnr-replace> wrapping an <img> tag.
			$search_pattern = $is_highlighted
				? '/<besnr-highlight\b[^>]*>(<img\s+[^>]*src=["\']' . preg_quote( $search_input, '/' ) . '["\'][^>]*>)<\/besnr-highlight>/is'
				: '/<besnr-replace\b[^>]*>(<img\s+[^>]*src=["\']' . preg_quote( $search_input, '/' ) . '["\'][^>]*>)<\/besnr-replace>/is';

			// Perform the replacement.
			return preg_replace_callback(
				$search_pattern,
				function ( $matches ) use ( $replace_input ) {
					// Extract the <img> tag from the matches.
					$original_img_tag = $matches[1];

					// Replace the `src` attribute value in the original <img> tag with the new value.
					$updated_img_tag = preg_replace(
						'/src=["\'][^"\']*["\']/',
						'src="' . esc_attr( $replace_input ) . '"',
						$original_img_tag
					);

					// Return the updated <img> tag without the wrapping <besnr-highlight> or <besnr-replace>.
					return $updated_img_tag;
				},
				$content
			);
		}

		/**
		 * Highlight multiple terms in the HTML content.
		 */
		public function highlight_multiple_terms( $content, $search_inputs, $is_highlighted, $is_case_sensitive ) {
			if ( empty( $search_inputs ) ) {
				return $content; // Return original content if no search terms are provided.
			}

			// Convert comma-separated phrases into an array and sanitize each term
			$terms = array_map( 'trim', explode( ',', $search_inputs ) );

			// Build a regular expression pattern for all terms
			$escaped_terms = array_map(
				function ( $term ) {
					return preg_quote( $term, '/' );
				},
				$terms
			);

			$modifiers = $is_case_sensitive ? '' : 'i';

			$pattern = '/\b(' . implode( '|', $escaped_terms ) . ')\b/' . $modifiers;

			// Perform highlighting
			$highlighted_content = preg_replace_callback(
				$pattern,
				function ( $matches ) use ( $is_highlighted ) {
					$terms = $matches[0];
					return $is_highlighted
						? '<besnr-highlight>' . $terms . '</besnr-highlight>'
						: '<besnr-replace>' . $terms . '</besnr-replace>';
				},
				$content
			);

			return $highlighted_content;
		}

		/**
		 * Replace highlighted multiple terms in the HTML content.
		 */
		public function replace_multiple_terms( $content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive ) {
			if ( empty( $search_input ) || empty( $replace_input ) ) {
				return $content; // Return original content if any required field is empty
			}

			$search_terms  = array_map( 'trim', explode( ',', $search_input ) );
			$replace_terms = array_map( 'trim', explode( ',', $replace_input ) );

			$modifiers = $is_case_sensitive ? '' : 'i';

			// Loop through each term and replace
			foreach ( $search_terms as $index => $search_term ) {
				$replace_term = $replace_terms[ $index ];

				$search_pattern = $is_highlighted
					? '/<besnr-highlight\b[^>]*>' . preg_quote( $search_term, '/' ) . '<\/besnr-highlight>/' . $modifiers
					: '/<besnr-replace\b[^>]*>' . preg_quote( $search_term, '/' ) . '<\/besnr-replace>/' . $modifiers;

				$content = preg_replace( $search_pattern, $replace_term, $content );
			}

			return $content;
		}

		/**
		 * Extract plain text from HTML using DOMDocument.
		 */
		private function highlight_extract_content( $html_content ) {
			$dom = new \DOMDocument();

			@$dom->loadHTML( // phpcs:ignore
				mb_convert_encoding( $html_content, 'HTML-ENTITIES', 'UTF-8' ),
				LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
			);

			return $dom->textContent; // phpcs:ignore
		}

		/**
		 * Preserve case-sensitive or insensitive highlighting.
		 */
		private function highlight_case_sensitive_content( $content, $search_input, $is_highlighted, $is_case_sensitive ) {
			// Build the regex pattern based on case sensitivity.
			$modifiers = $is_case_sensitive ? '' : 'i';

			$pattern = '/\b' . preg_quote( $search_input, '/' ) . '\b/' . $modifiers;

			// Perform highlighting with preserved case.
			$highlighted_content = preg_replace_callback(
				$pattern,
				function ( $matches ) use ( $is_highlighted ) {
					$text = $matches[0];
					// Wrap the matched text with <besnr-replace>
					return $is_highlighted
						? '<besnr-highlight>' . $text . '</besnr-highlight>'
						: '<besnr-replace>' . $text . '</besnr-replace>';
				},
				$content
			);

			return $highlighted_content;
		}
	}

	$besnr = new Block_Editor_Search_Replace();
	$besnr->init();
}
