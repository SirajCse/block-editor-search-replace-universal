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
        public function count_occurrences($content, $search_replace_input, $is_highlighted, $is_case_sensitive) {
            // 1. Normalize all encodings and clean content
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $content = preg_replace('/\s+/u', ' ', $content); // Normalize whitespace
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8'); // Force UTF-8

            // 2. Process search terms
//            $search_terms = array_map('trim',
//                strpos($search_replace_input, ',') !== false
//                    ? explode(',', $search_replace_input)
//                    : [$search_replace_input]
//            );

            $search_terms = strpos($search_replace_input, ',') !== false
                ? explode(',', $search_replace_input)
                : [$search_replace_input];


            // 3. Prepare pattern modifiers
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u';
            $total_count = 0;

            foreach ($search_terms as $term) {
                // Clean and normalize the search term
                $term = html_entity_decode($term, ENT_QUOTES, 'UTF-8');
                $term = mb_convert_encoding($term, 'UTF-8', 'UTF-8');

                // Debug: Verify term exists in content
                $pos = mb_strpos($content, $term);
                if ($pos === false) {
                    continue; // Term doesn't exist at all
                }

                // 4. Create flexible matching patterns
                $patterns = [
                    // Pattern for highlighted/replaced content
                    '/<besnr-(?:highlight|replace)[^>]*>([^<]*)' .
                    preg_quote($term, '/') .
                    '([^<]*)<\/besnr-(?:highlight|replace)>/' . $modifiers,

                    // Simple text pattern (without tags)
                    '/' . preg_quote($term, '/') . '/' . $modifiers
                ];

                // 5. Try all patterns until we find matches
                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        $total_count += count($matches[0]);
                        break;
                    }
                }
            }

            return $total_count;
        }

        /**
		 * Highlight (plain) text-only and re-apply HTML structure.
		 */
        public function highlight_text_only($html_content, $search_input, $is_highlighted = true, $is_case_sensitive = false) {
            if (empty($search_input)) {
                return $html_content;
            }

            // Normalize encoding for safe DOM processing
            $html_content = mb_convert_encoding($html_content, 'HTML-ENTITIES', 'UTF-8');
            $search_input = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');

            // Create DOM document
            $dom = new \DOMDocument();
            $dom->substituteEntities = false;

            // Load HTML with UTF-8 compatibility
            @$dom->loadHTML(
                '<?xml encoding="UTF-8">' . $html_content,
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            // Remove XML declaration
            foreach ($dom->childNodes as $item) {
                if ($item->nodeType === XML_PI_NODE) {
                    $dom->removeChild($item);
                }
            }
            $dom->encoding = 'UTF-8';

            // XPath to get text nodes excluding script and style
            $xpath = new \DOMXPath($dom);
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u'; // Unicode-aware
            $pattern = '/(' . preg_quote($search_input, '/') . ')/' . $modifiers;

            foreach ($xpath->query('//text()[not(ancestor::script) and not(ancestor::style)]') as $text_node) {
                $original_text = $text_node->nodeValue;

                // Skip empty nodes
                if (trim($original_text) === '') {
                    continue;
                }

                // Replace matches with highlight or replace tags
                $updated_text = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($is_highlighted) {
                        $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';
                        return "<$tag>" . htmlspecialchars($matches[0], ENT_QUOTES, 'UTF-8') . "</$tag>";
                    },
                    $original_text
                );

                // Replace node only if it changed
                if ($updated_text !== $original_text) {
                    $new_node = $dom->createDocumentFragment();
                    @$new_node->appendXML($updated_text);
                    $text_node->parentNode->replaceChild($new_node, $text_node);
                }
            }

            // Return final HTML
            $output = $dom->saveHTML();
            return mb_convert_encoding($output, 'UTF-8', 'HTML-ENTITIES');
        }


        /**
		 * Replace highlighted text-only from the HTML content.
		 */
        public function replace_text_only($content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive) {
            // Basic validation
            if (empty($content) || empty($search_input)) {
                return $content;
            }

            // Normalize all strings to UTF-8
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $search_input = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');
            $replace_input = html_entity_decode($replace_input ?? '', ENT_QUOTES, 'UTF-8');

            // Create regex modifiers
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u'; // 'u' is CRUCIAL for Bengali

            // Determine which tag we're working with
            $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';

            // Build the pattern with proper Unicode support
            $pattern = '/
        (<'.$tag.'\b[^>]*>)   # Opening tag with attributes
        (.*?)                 # Content before match
        (\b'.preg_quote($search_input, '/').'\b) # The text to replace
        (.*?)                 # Content after match
        (<\/'.$tag.'>)        # Closing tag
    /x'.$modifiers;

            // Perform the replacement
            $result = preg_replace_callback(
                $pattern,
                function($matches) use ($replace_input) {
                    // Reconstruct with replacement while preserving tags
                    return $matches[1].$matches[2].$replace_input.$matches[4].$matches[5];
                },
                $content,
                -1,
                $count
            );

            // Return results based on what actually happened
            if ($result === null) {
                error_log("Regex error: ".preg_last_error_msg());
                return $content;
            }

            if ($count > 0) {
                return $result;
            }

            // Fallback to simple replacement if no tagged matches found
            return str_replace($search_input, $replace_input, $content);
        }

        /**
		 * Highlight URLs from the HTML content.
		 *
		 * Tags: <a>
		 * Attributes: href
		 */
        public function highlight_urls($html_content, $search_input, $is_highlighted = true) {
            if (empty($search_input)) {
                return $html_content;
            }

            // Convert to HTML entities for safe parsing
            $html_content = mb_convert_encoding($html_content, 'HTML-ENTITIES', 'UTF-8');

            $dom = new \DOMDocument();
            @$dom->loadHTML(
                '<?xml encoding="UTF-8">' . $html_content,
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            // Remove XML declaration (added for encoding)
            foreach ($dom->childNodes as $item) {
                if ($item->nodeType === XML_PI_NODE) {
                    $dom->removeChild($item);
                }
            }

            $xpath = new \DOMXPath($dom);
            $links = $xpath->query('//a[@href]');

            foreach ($links as $link) {
                $href = html_entity_decode($link->getAttribute('href'), ENT_QUOTES, 'UTF-8');
                $search_input_normalized = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');

                // Unicode-safe case-insensitive match
                if (mb_stripos($href, $search_input_normalized, 0, 'UTF-8') !== false) {
                    $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';
                    $wrapper = $dom->createElement($tag);
                    $link->parentNode->replaceChild($wrapper, $link);
                    $wrapper->appendChild($link);
                }
            }

            $output = $dom->saveHTML();
            return mb_convert_encoding($output, 'UTF-8', 'HTML-ENTITIES');
        }


        /**
		 * Replace highlighted URLs from the HTML content.
		 */
        public function replace_urls($content, $search_input, $replace_input, $is_highlighted) {
            // Early return if invalid inputs
            if (empty($search_input) || empty($replace_input)) {
                return $content;
            }

            // Normalize URLs and content encoding
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $search_input = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');
            $replace_input = html_entity_decode($replace_input, ENT_QUOTES, 'UTF-8');

            // Create pattern with Unicode support
            $modifiers = 'isu'; // 'i' (case-insensitive), 's' (dot matches newline), 'u' (Unicode)
            $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';

            // Improved pattern that handles:
            // 1. Various quote styles (single/double)
            // 2. URL-encoded characters
            // 3. Whitespace variations
            $pattern = '/
        <'.$tag.'\b[^>]*>          # Opening wrapper tag
        (<a\b[^>]*href=)            # Anchor tag start
        (["\'])                     # Quote mark
        ('.preg_quote($search_input, '/').')  # URL to replace
        \2                          # Matching quote mark
        ([^>]*>)                    # Rest of opening a tag
        (.*?)                       # Link text
        (<\/a>)                     # Closing a tag
        <\/'.$tag.'>                # Closing wrapper tag
    /x'.$modifiers;

            // Perform the replacement
            $result = preg_replace_callback(
                $pattern,
                function($matches) use ($replace_input) {
                    // Reconstruct the link with new URL while preserving all other attributes
                    return $matches[1].$matches[2].esc_attr($replace_input).$matches[2].$matches[4].$matches[5].$matches[6];
                },
                $content,
                -1,
                $count
            );

            // Fallback to simpler replacement if no matches found with full pattern
            if ($count === 0) {
                $simple_pattern = '/(href=["\'])'.preg_quote($search_input, '/').'(["\'])/'.$modifiers;
                $result = preg_replace($simple_pattern, '$1'.esc_attr($replace_input).'$2', $content);
            }

            return $result !== null ? $result : $content;
        }

		/**
		 * Highlight images from the HTML content.
		 *
		 * Tags: <img/>
		 * Attributes: src
		 */
        public function highlight_images($html_content, $search_input, $is_highlighted = true) {
            if (empty($search_input)) {
                return $html_content;
            }

            // Normalize and safely encode the HTML
            $html_content = mb_convert_encoding($html_content, 'HTML-ENTITIES', 'UTF-8');

            $dom = new \DOMDocument();
            @$dom->loadHTML(
                '<?xml encoding="UTF-8">' . $html_content,
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            // Remove XML encoding declaration
            foreach ($dom->childNodes as $item) {
                if ($item->nodeType === XML_PI_NODE) {
                    $dom->removeChild($item);
                }
            }

            $xpath = new \DOMXPath($dom);
            $images = $xpath->query('//img[@src]');

            foreach ($images as $image) {
                $src = html_entity_decode($image->getAttribute('src'), ENT_QUOTES, 'UTF-8');
                $search_input_normalized = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');

                // Unicode-safe case-insensitive comparison
                if (mb_stripos($src, $search_input_normalized, 0, 'UTF-8') !== false) {
                    $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';
                    $wrapper = $dom->createElement($tag);
                    $image->parentNode->replaceChild($wrapper, $image);
                    $wrapper->appendChild($image);
                }
            }

            $output = $dom->saveHTML();
            return mb_convert_encoding($output, 'UTF-8', 'HTML-ENTITIES');
        }


        /**
		 * Replace highlighted images from the HTML content.
		 */
        public function replace_images($content, $search_input, $replace_input, $is_highlighted) {
            // Early return for empty inputs
            if (empty($search_input) || empty($replace_input)) {
                return $content;
            }

            // Normalize encoding and clean inputs
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $search_input = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');
            $replace_input = html_entity_decode($replace_input, ENT_QUOTES, 'UTF-8');

            // Create pattern with Unicode support
            $modifiers = 'isu'; // Case-insensitive, dot matches newline, Unicode
            $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';

            // Improved pattern that handles:
            // 1. Various quote styles
            // 2. URL-encoded characters
            // 3. Whitespace variations
            // 4. Other img attributes
            $pattern = '/
        <'.$tag.'\b[^>]*>         # Opening wrapper tag
        (<img\b[^>]*\bsrc=)       # img tag start with src attribute
        (["\'])                    # Quote mark
        ('.preg_quote($search_input, '/').')  # URL to replace
        \2                         # Matching quote mark
        ([^>]*>)                   # Rest of img tag
        <\/'.$tag.'>               # Closing wrapper tag
    /x'.$modifiers;

            // Perform the replacement
            $result = preg_replace_callback(
                $pattern,
                function($matches) use ($replace_input) {
                    // Reconstruct the img tag with new src while preserving other attributes
                    return $matches[1].$matches[2].esc_url($replace_input).$matches[2].$matches[4];
                },
                $content,
                -1,
                $count
            );

            // Fallback to simpler replacement if no matches found with full pattern
            if ($count === 0) {
                $simple_pattern = '/(<img\b[^>]*\bsrc=)(["\'])'.preg_quote($search_input, '/').'(\2)/'.$modifiers;
                $result = preg_replace($simple_pattern, '$1$2'.esc_url($replace_input).'$3', $content);
            }

            return $result !== null ? $result : $content;
        }

		/**
		 * Highlight multiple terms in the HTML content.
		 */
        public function highlight_multiple_terms($content, $search_inputs, $is_highlighted, $is_case_sensitive) {
            // Early return if no search terms
            if (empty($search_inputs)) {
                return $content;
            }

            // Normalize content encoding
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

            // Process search terms
//            $terms = array_map('trim', explode(',', $search_inputs));
            $terms = explode(',', $search_inputs);

            $terms = array_filter($terms); // Remove empty terms
            $terms = array_unique($terms); // Remove duplicates

            // Build regex pattern with Unicode support
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u'; // 'u' modifier for Unicode
            $escaped_terms = array_map(function($term) {
                $term = html_entity_decode($term, ENT_QUOTES, 'UTF-8');
                return preg_quote($term, '/');
            }, $terms);

            // Improved pattern that handles:
            // 1. Unicode word boundaries (\b doesn't work well with Unicode)
            // 2. Multiple terms
            // 3. Maintains original capitalization
            $pattern = '/(?<=^|\W)(' . implode('|', $escaped_terms) . ')(?=\W|$)/' . $modifiers;

            // Perform highlighting with better replacement
            $highlighted_content = preg_replace_callback(
                $pattern,
                function($matches) use ($is_highlighted) {
                    $matched_text = $matches[0];
                    return $is_highlighted
                        ? '<besnr-highlight>' . esc_html($matched_text) . '</besnr-highlight>'
                        : '<besnr-replace>' . esc_html($matched_text) . '</besnr-replace>';
                },
                $content
            );

            // Fallback for preg_replace failure
            if ($highlighted_content === null) {
                error_log('Highlighting failed with error: ' . preg_last_error_msg());
                return $content;
            }

            return $highlighted_content;
        }

		/**
		 * Replace highlighted multiple terms in the HTML content.
		 */
        public function replace_multiple_terms($content, $search_input, $replace_input, $is_highlighted, $is_case_sensitive) {
            // Basic validation
            if (empty($content) || empty($search_input) || empty($replace_input)) {
                return $content;
            }

            // Normalize all strings to UTF-8
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $search_terms = array_map(function($term) {
                return html_entity_decode(trim($term), ENT_QUOTES, 'UTF-8');
            }, explode(',', $search_input));
            $replace_terms = array_map(function($term) {
                return html_entity_decode(trim($term), ENT_QUOTES, 'UTF-8');
            }, explode(',', $replace_input));

            // Create regex modifiers - 'u' is CRUCIAL for Bengali/Unicode
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u';

            // Determine which tag we're working with
            $tag = $is_highlighted ? 'besnr-highlight' : 'besnr-replace';

            // Loop through each term and replace
            foreach ($search_terms as $index => $search_term) {
                if (!isset($replace_terms[$index])) {
                    continue; // Skip if no corresponding replacement term
                }

                $replace_term = $replace_terms[$index];

                // Build the pattern similar to replace_text_only
                $pattern = '/
            (<'.$tag.'\b[^>]*>)   # Opening tag with attributes
            (.*?)                 # Content before match
            (\b'.preg_quote($search_term, '/').'\b) # The text to replace
            (.*?)                 # Content after match
            (<\/'.$tag.'>)        # Closing tag
        /x'.$modifiers;

                $content = preg_replace_callback(
                    $pattern,
                    function($matches) use ($replace_term) {
                        // Reconstruct with replacement while preserving tags
                        return $matches[1].$matches[2].$replace_term.$matches[4].$matches[5];
                    },
                    $content
                );
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
        private function highlight_case_sensitive_content($content, $search_input, $is_highlighted, $is_case_sensitive) {
            // Basic validation
            if (empty($content) || empty($search_input)) {
                return $content;
            }

            // Normalize strings to UTF-8
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $search_input = html_entity_decode($search_input, ENT_QUOTES, 'UTF-8');

            // Create regex modifiers - 'u' is CRUCIAL for Bengali/Unicode
            $modifiers = ($is_case_sensitive ? '' : 'i') . 'u';

            // Build the regex pattern with word boundaries that work with Unicode
            $pattern = '/(?<!\pL)' . preg_quote($search_input, '/') . '(?!\pL)/' . $modifiers;

            // Perform highlighting with preserved case
            $highlighted_content = preg_replace_callback(
                $pattern,
                function ($matches) use ($is_highlighted) {
                    $text = $matches[0];
                    // Wrap the matched text with appropriate tags
                    return $is_highlighted
                        ? '<besnr-highlight>' . $text . '</besnr-highlight>'
                        : '<besnr-replace>' . $text . '</besnr-replace>';
                },
                $content
            );

            // Handle potential regex errors
            if ($highlighted_content === null) {
                error_log("Regex error in highlighting: " . preg_last_error_msg());
                return $content;
            }

            return $highlighted_content;
        }
    }

	$besnr = new Block_Editor_Search_Replace();
	$besnr->init();
}
