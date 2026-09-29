=== Dynamic Tags for Elementor ===
Contributors: nisan92
Tags: elementor, dynamic-tags, acf, woocommerce, wpml
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.4.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Supercharge Elementor Free with 100+ custom dynamic tags. Unlocks WooCommerce, ACF, SCF, and advanced system metrics in Elementor.

== Description ==

**Dynamic Tags for Elementor** is the ultimate dynamic content toolkit. It enables **100+ custom dynamic tags** in Elementor Free, organized into purpose-built groups. When upgraded to Pro, it unlocks **210+ additional premium tags** across Post Pro, Media Pro, Author Pro, Site Pro, WooCommerce Pro, EasyCommerce Pro, ACF Pro, SCF Pro, Taxonomy Pro, EventPrime Pro, and PublishPress Authors Pro.

Our advanced caching system with request-scoped memoization for repeated field lookups and product queries, and transient-based admin caching with smart invalidation, delivers faster page rendering and reduced database queries.

= Features =
* **Post & Archive** — Post title, date, content, categories, comments, excerpt, terms, parent, archive titles, and descriptions.
* **Site & Media** — Site title, URL, tagline, logo, icon, featured images, and galleries.
* **Author & User** — Author details, bio, profile pictures, user roles, capabilities, and profile metrics.
* **Actions & Comments** — Dynamic links for lightboxes, off-canvas, reload, and comment forms.
* **WooCommerce** — Product price, SKU, stock status, categories, and reviews.
* **EasyCommerce Pro** — 28 new dynamic tags covering products, categories, brands, downloads, cart, and store metrics.
* **ACF & SCF Parity** — 13 custom field tags supporting repeaters, galleries, relationships, date-time, maps, and option pages.
* **Pro Version (Upgrade)** — Unlocks 210+ premium tags including WooCommerce Pro (34 tags), EasyCommerce Pro (28 tags), ACF Pro (34 tags), SCF Pro (34 tags), EventPrime Pro (15 tags), PublishPress Authors Pro (8 tags), Taxonomy Pro (10 tags), and the WordPress Pro suite: Post Pro (16 tags), Media Pro (6 tags), Author Pro (12 tags), and Site Pro (13 tags).

Use for dynamic text, URLs, images, and conditional outputs in any Elementor Free widget.

== Video Tutorial ==

Watch this quick overview video to see how Dynamic Tags for Elementor works in action:

https://www.youtube.com/watch?v=9rvMCf-gUFE

== Installation ==

1. Upload plugin files to `/wp-content/plugins/dynamic-tags-for-elementor`.
2. Activate via Plugins menu.
3. Open Elementor editor → click Dynamic Tags icon on any supported field → choose a tag.

For quick tag discovery, go to **WP Admin → Dynamic Tags → Tag Directory**.

== Frequently Asked Questions ==

= Does this require Elementor Pro? =
No. This plugin works exclusively with Elementor Free. All dynamic tags are designed for Elementor Free and do not require a Pro license.

= Will this conflict with Elementor Pro tags? =
No. DTE tags are registered in separate groups and coexist peacefully with Elementor Pro tags. If you later upgrade to Pro, both tag sets remain available.

= Which custom fields and third-party plugins are supported? =
DTE supports:
* **WooCommerce** (Product price, SKU, stock status, categories, ratings, add-to-cart URLs).
* **EasyCommerce** (28 Pro tags: Product title, SKU, price, stock, ratings, downloads, cart, store metrics).
* **ACF (Advanced Custom Fields)** (13 tags including repeaters, galleries, relationships, true/false, taxonomy terms, dates).
* **SCF (Secure Custom Fields)** (13 tags with feature parity to ACF).
* **WPML & Polylang** (Automatic language-aware URL filtering).

= Can I use these tags in all Elementor widgets? =
Most widgets with text, URL, or image fields support dynamic tags (indicated by the small Dynamic Tag icon next to the field).

= How do I find a specific tag quickly? =
Go to **WP Admin → Dynamic Tags → Tag Directory** to view a searchable list of all tags grouped by category with descriptions.

= Can I register custom dynamic tags? =
Yes. DTE provides filters and action hooks for third-party developers, including `dynatafo_dynamic_tag_groups` and `dynatafo_register_dynamic_tags`.

== Dependencies ==

* **Required**: Elementor
* **Optional**: Advanced Custom Fields (ACF), Secure Custom Fields (SCF), WooCommerce, EasyCommerce, EventPrime – Events Calendar, Bookings and Tickets Plugin by Metagauss, PublishPress Authors, WPML, Polylang

== Changelog ==

= 1.4.5 =
* Compatibility: Synced compatibility and version alignment with Dynamic Tags for Elementor Pro v1.4.5.
* Update: Bumped plugin version to 1.4.5.

= 1.4.4 =
* Feature: Added PublishPress Authors Pro integration – 8 new dynamic tags for PublishPress Authors with support for Multiple Authors, Guest Authors, and author archives (available in Pro version).
* Compatibility: Synced compatibility and version alignment with Dynamic Tags for Elementor Pro v1.4.4.
* Update: Bumped plugin version to 1.4.4.

= 1.4.3 =
* Compatibility: Verified compatibility with Elementor 4.2.4, Elementor Pro 4.2.3, and WordPress 7.1.
* Feature: Added Magic Copy for Elementor Pro to the Other Plugins ecosystem.
* Update: Bumped plugin version to 1.4.3.

= 1.4.2 =
* Compatibility: Verified compatibility with Elementor 4.2.3, Elementor Pro 4.2.2, and WordPress 7.1.
* Security: Updated security patch and general maintenance fixes.

= 1.4.1 =
* Performance: Optimized dynamic tag registration path in loader by using precompiled static definition maps and eliminating array_merge() calls from the hot path.

= 1.4.0 =
* Fix: Resolved PHP 8.4 TypeError crash in Elementor Editor caused by calling get_settings() in the Internal URL (DTE) tag prior to control stack initialization. Thanks @nomade23 for pointing the issue.

= 1.3.9 =
* Feature: Added EventPrime Pro integration – 15 new dynamic tags for EventPrime – Events Calendar, Bookings and Tickets Plugin by Metagauss (available in Pro version).
* Compatibility: Elementor compatibility verified for the latest version.
* Security: Security patch updated.

= 1.3.8 =
* Fix: The Internal URL (DTE) tag now correctly shows all public WordPress post types in the Content dropdown.
* Update: Third-party plugin support for EventPrime – Events Calendar, Bookings and Tickets Plugin by Metagauss.
* Update: Search and page controls in the Internal URL (DTE) tag fallback selector now correctly filter and paginate results. Thanks @pauljbrown for pointing this issue.
* Update: UX updated.
* Compatibility: Elementor compatibility verified for the latest version.
* Security: Security patch updated.

= 1.3.7 =
* Fixed: Resolved post loop query context issue where dynamic tags (e.g. ACF/SCF fields) returned data from the first post for all loop items.
* Compatibility: Verified compatibility with Elementor and Elementor Pro up to version 4.2.1.
* Compatibility: Adjusted Tested up to header format in readme.txt to align with WordPress.org standards.


== Upgrade Notice ==

= 1.4.5 =
Upgrade to 1.4.5 for synchronized compatibility with Dynamic Tags for Elementor Pro v1.4.5.

