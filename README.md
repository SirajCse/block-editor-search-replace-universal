<!-- @format -->

### Block Editor Search & Replace - Text, Links & Images

Contributors: krasenslavov, developry
Donate Link: https://krasenslavov.com/hire-krasen/
Tags: block editor, blocks, classic editor, search, replace
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.2.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easily search and replace text, images or links in the Block Editor, with backward compatibility for the Classic Editor.

## DESCRIPTION

Easily search and replace text, images or links in the Block Editor, with backward compatibility for the Classic Editor.

https://www.youtube.com/embed/zWxPv8pJH4U

Elevate your editing experience with our [**Block Editor Search & Replace**](https://bit.ly/3PDk36N) plugin!

Designed to seamlessly integrate into the WordPress environment, this plugin allows you to swiftly locate and replace any, text, images or links within the Block Editor.

[**Block Editor Search & Replace**](https://bit.ly/3PDk36N) is engineered to cater to both modern and traditional workflows, offering full compatibility with the Classic Editor.

## USAGE

After installing and activating [**Block Editor Search & Replace**](https://bit.ly/3PDk36N), a new meta box labeled Search & Replace will be accessible in your page or post editing screen. Here's how it works:

1. Select your search and replace method.
2. Enter your search phrase to instantly highlight matching keywords within the content.
3. Input your desired replacement text and click the **Replace** button to execute the change.
4. Adjust settings on-the-fly, toggling the highlighter and case sensitivity options as needed.
5. Use the **Remove Tags** button to remove any custom HTML tags created by the highlighter, restoring the text to its original state.

## FEATURES & LIMITATIONS

[**Block Editor Search & Replace**](https://bit.ly/3PDk36N) enhance your productivity with these streamlined features:

1. **Search & Replace:** A familiar, intuitive search and replace functionality.
2. **Highlighter:** Visual cues highlight all search hits, making editing more efficient.
3. **Case Sensitivity:** Flexibility to conduct case-sensitive or insensitive searches and replacements.
4. **Images and Links:** Ability to search and replace any image `<img src="...">` or links `<a href="..."></a>`
5. **Multiple Terms:** Search and replace multipl terms separating them with comma.
6. **Remove Tags:** Easily remove all custom tags added for highlighting and replacing the search content.

**Convenient User Settings**

While the free [**Block Editor Search & Replace**](https://bit.ly/3PDk36N) doesn't have a separate settings page, all configurations are conveniently located under `Block Editor S/R` or `Tools > Block Editor S/R` for compact mode. This includes:

- Toggle support between Block (Gutenberg) and Classic editors.
- Select and limit the roles allowed to access plugin features.
- Select and limit the post types supported by the plugin.

### Known Issues and Limitations

- Sometimes when using the plugin with `image` (switching between Classic -> Block editor) method in the Block Editor, after performing a search, you will see the **Attempt recovery**, this means the image is marked for replacement, ignore add your replce phrase and proceed with the replacement action.
- When you select image URLs directly from the WP media modal window and you need to target image e.g. 1024x768px you need to adjust the URLs and auto populate will add the full image URL.

## DETAILED DOCUMENTATION

Find step-by-step setup guides, usage instructions, demos, videos, and insights on the [**Block Editor Search & Replace Pro**](https://searchreplaceblocks.com/help) page.

## BLOCK EDITOR SEARCH & REPLACE PRO

Upgrade to the Pro version of **Block Editor Search & Replace** and access these powerful features:

- **Search & Replace for CPTs:** Extend functionality to custom post types.
- **Dry-run with Preview:** Safely preview changes before applying them.
- **Shortcodes, HTML, and RegEx:** Advanced support for dynamic content and complex patterns.
- **Partial Image and Link Replacement:** Update specific portions of image and link URLs.
- **Remove Text Limitations:** Disable character limits and text sanitization for unrestricted edits.
- **Multilingual Compatibility:** Seamlessly manage content across multiple languages.
- **Backup and Restore:** Secure your changes with dedicated backup and restore functionality.
- **Priority Email Support:** Get fast, expert help when you need it.
- **First-Release Updates:** Enjoy early access to the latest improvements and features.

Upgrade now to maximize efficiency and enhance your editing capabilities. Learn more at [**Block Editor Search & Replace Pro**](https://searchreplaceblocks.com/).

## FREQUENTLY ASKED QUESTIONS

Visit the [**Support**](https://wordpress.org/support/plugin/block-editor-search-replace/) page to share your questions or requests.

We usually respond to tickets within a few days.

Feature requests are added to our wish list and considered for future updates.

### Is This Plugin Compatible with the Classic Editor?

**Absolutely!** [**Block Editor Search & Replace**](https://bit.ly/3PDk36N) ensures support and backward compatibility with the Classic Editor.

### Can This Plugin Be Used with Custom Post Types (CPT) or WooCommerce?

**Certainly!** This feature is limited to only the [**Block Editor Search & Replace Pro**](https://bit.ly/3PDk36N) version of the plugin.

### Are Revisions Saved by This Plugin?

**No**, to avoid cluttering your database with unnecessary revisions, the revision feature is disabled. However, you can utilize the Update button at the conclusion of your post or page editing process to save a standard WordPress revision.

### What If I Need Additional Support?

**Absolutely!** For any issues or queries, feel free to reach out through the contact form available on the [**Block Editor Search & Replace**](https://bit.ly/3PDk36N) website. We're here to help!

## SCREENSHOTS

Below are screenshots showing how to access and use the plugin in WordPress.

1. screenshot-1.(png)
2. screenshot-2.(png)
3. screenshot-3.(png)
4. screenshot-4.(png)
5. screenshot-5.(png)

## INSTALLATION

The plugin installation is easy and straightforward. Let us know if you run into any issues.

= Installation from WordPress =

1. Go to **Plugins > Add New**.
2. Search for **Block Editor Search & Replace**.
3. Install and activate the plugin.
4. Click **Settings** or go to **Block Edior S/R** in the menu.

= Manual Installation =

1. Upload the `block-editor-search-replace` folder to `/wp-content/plugins/`.
2. Go to **Plugins**.
3. Activate the **Block Editor Search & Replace** plugin.
4. Click **Settings** or navigate to **Block Edior S/R** in the menu.

= After Activation =

1. Go to any post/page and you will see plugin controls (metabox) in the sidebar.
2. The plugin has support to both Block/Classic ediors.

## CHANGELOG

= 1.2.6 =

- Update - Compatibility tested with WordPress 6.8

= 1.2.5 =

- New - Hide setting options notices after save
- Update - Performance and optimization compatibility
- Update - Improve overall code quality
- Update - Change year from 2024 -> 2025 all over
- Fix - Minor JS and CSS fixes

= 1.2.4 =

- New - New search methods, image and link URLs, multiple terms
- New - Restrict and select roles allowed to access plugin features
- New - New code for search & replace actions both for PHP and JS
- New - Add `besnr-replace` in addition to `besnr-highlight`
- Update - the HTML and JS to match the Pro version for the metabox/controls
- Update - Pro plugin features, pro table, links, admin notice, etc.
- Update - Remove full-text support option
- Fix - Block editor breaking to and need "Attemp to recovery"

= 1.2.3 =

- Update - Development env setup and CSS assets updates

= 1.2.2 =

- Update - Performance and optimization compatibility

= 1.2.1 =

- New - Add compact mode toggle under settings option
- Update - Compatibility check with WordPress 6.7
- Update - Language file (.pot)
- Update - Language file (.pot) header text
- Update - Change license files to use GPLv3

**Check out the complete changelog on our [**Block Editor Search & Replace**](https://bit.ly/3PDk36N) website.**

## UPGRADE NOTICE

Upgrade to [**Block Editor Search & Replace Pro**](https://bit.ly/3PDk36N) for advanced features, unlimited recovery, and priority support!
