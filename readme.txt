=== Ramisa Online Chat ===
Contributors: shcd
Tags: online chat, floating button, contact button, support, rtl
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A polished floating chat widget with a native settings panel, custom database storage, contact buttons, working hours, quick replies, RTL support, live preview, and shortcode output.

== Description ==

Ramisa Online Chat helps site owners add a refined floating contact widget to their WordPress website. Administrators can set a public chat URL or a click-to-chat number, customize the support profile, choose the visual style, configure working hours, add additional contact options, display quick reply buttons, preview the widget in the dashboard, and place inline buttons with shortcodes.

The WordPress.org package is fully functional. All included features work after activation without license keys, trial periods, usage limits, or hidden upgrade gates. The package includes editable PHP, CSS, and JavaScript files and does not ship with obfuscated code or build-only block bundles. Settings are stored in a dedicated Ramisa database table and are removed when the plugin is deleted from WordPress.

Features:

* Floating online chat widget
* Dedicated Ramisa Chat dashboard menu
* Responsive and RTL-ready layout
* Native neumorphic settings panel with vertical sections
* Dedicated custom database table for plugin settings
* Developer card with SHABNAM.DEV and the plugin version read from the plugin header
* Local destination status indicator with green/red state for the configured chat destination
* Public chat URL field and optional click-to-chat number fallback
* Live admin preview for profile, text, theme, and icon changes
* Custom button text, helper label, profile name, role, message, avatar, card title, and card subtitle
* Extra support contacts with separate names, titles, images, URLs, optional avatars, grid/list layout, and optional search
* Up to three quick reply buttons
* Working hours with timezone and per-day start/end schedule
* Optional hide-while-offline behavior
* Left or right screen position
* Emerald, blue, violet, gold, and dark color palettes
* Small, normal, and large button sizes
* Rounded, circular, and pill button shapes
* Neumorphic, glass, and compact card styles
* Soft entrance, pulse, bounce, floating, or reduced-motion friendly no-animation mode
* Optional automatic card opening
* Optional visitor time display
* Optional notification badge
* Shortcodes: [ramisa_online_chat] and [ramisa_online_chat_button]

== External services ==

This plugin does not send data to an external service from the server side and does not call a remote API. It only opens the public chat destination configured by the site administrator after a visitor clicks a link or button.

If a configured chat destination points to a third-party messaging or contact platform, the visitor's browser opens that third-party website or app directly. The site administrator is responsible for choosing the destination and for linking to the relevant terms and privacy policy where appropriate.

== Data storage ==

Ramisa Online Chat stores its settings in a dedicated custom table named with the active WordPress database prefix, followed by ramisa_online_chat_settings. This keeps the plugin settings separate from posts, post meta, and regular option rows. The table is created on activation and removed by uninstall.php when the plugin is deleted from WordPress. Deactivation alone does not delete the table, so settings are not lost during routine troubleshooting.

== Source code ==

The package includes editable PHP, CSS, and JavaScript source files. It does not include a compiled block bundle and does not require a build step for the included features.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/ or install the ZIP file from the WordPress admin.
2. Activate the plugin.
3. Open the Ramisa Chat menu in the WordPress dashboard.
4. Enter a chat destination and customize the widget.
5. Save the settings.

== Frequently Asked Questions ==

= Are all included features available? =

Yes. Every feature included in this WordPress.org package is available after activation.

= Does the plugin check a remote server? =

No. The dashboard indicator validates the configured destination locally. It does not ping a remote server or call a messaging API.

= Can I use a shortcode? =

Yes. Use [ramisa_online_chat] or [ramisa_online_chat_button]. You can also pass attributes such as text, url, theme, icon, and size.

= Does the plugin load remote fonts or scripts? =

No. The plugin uses local CSS and JavaScript files and does not load remote fonts or scripts.

== Screenshots ==

1. Floating chat card on the front end.
2. Vertical neumorphic settings panel.
3. Destination status indicator and developer card.
4. Multi-contact buttons and quick replies.
5. Working hours and availability settings.
6. Inline shortcode button.

== Changelog ==

= 1.0.0 =
* Initial WordPress.org release.
* Added the floating chat widget, dedicated Ramisa dashboard, custom database table, working hours, additional contacts, quick replies, live preview, and shortcode output.
* Kept the package fully functional without license checks, trial limits, hidden upgrade gates, obfuscated code, or build-only assets.

== Upgrade Notice ==

= 1.0.0 =
This release stores Ramisa settings in a dedicated database table and removes that table when the plugin is deleted.
