=== Shortcode-Finder ===
Contributors: Media Soil
Donate link: http://mediasoil.com/
Tags: shortcode, shortcodes, user, users, admin, notification
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin let's you see every shortcode available to your WordPress site.

== Description ==

Shortcode Finder is a WordPress plugin that allows you to find which shortcodes have been installed in your WordPress site.

In addition to native WordPress shortcodes, users can add shortcodes with plugins or themes that are installed and activated on their WordPress sites.

This plugin gives you a lookup table that lists out all shortcodes in your system, both on the admin side and user side of WordPress depending on which hook the author added the shortcode. In addition to the lookup table, users are given a button to add any shortcode when creating/editing a post/page.

**Plugin Requires PHP5**

== Installation ==

1. Upload the `shortcode-finder` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where can I find the shortcode lookup table? =

The table can be found under the `Tools` menu with the name `Shortcodes`.

= Why do you have two columns in the lookup table? =

Depending on how the shortcode author hooks into WordPress, some shortcodes may not be available to certain areas inside WordPress. 

"Available to Admins" means that the Shortcodes are available to any process hooking in to an [admin hook](http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_an_Admin_Page_Request) after the shortcode has hooked.

Similarly, "Available to Users" means that the Shortcodes are available to any process hooking in to a [user hook](http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request) after the shortcode has hooked.

Many hooks are shared across both admins and users so you may see duplicates.

== Changelog ==

= 0.1 =
* Plugin Created!