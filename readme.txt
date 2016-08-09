=== OM4 Header/Footer Code ===
Tags: code, html code, html, header, footer, javascript, head, body
Requires at least: 4.2
Tested up to: 4.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use the WordPress dashboard to add custom HTML code to the head section or closing body section. Also ensures jQuery is always available in the frontend.

== Description ==

Custom HTML code can be added to your site using an easy to use WordPress dashboard interface. No file editing required!

== Installation ==

1. Activate the plugin.
1. Go to Appearance, Header & Footer, and write some HTML code!

== Changelog ==

= 1.1 =
* Add HTML syntax highlighting/editing.

= 1.0.6 =
* Security enhancement for add_query_arg usage.

= 1.0.5 =
* Allow other plugins to perform actions whenever the Header/Footer code is saved.
* No longer flush the caches in this plugin. Instead, it will be done via the OM4 Service plugin.

= 1.0.4 =
* Automatically purge WP Engine's cache when header/footer code is saved.
* Add readme.

= 1.0.3 =
* Automatically flush W3 Total Cache's page cache when header/footer code is saved.

= 1.0.2 =
* Parse WordPress shortcodes in header code/script and footer/code script.
* Code improvements to more closely match the WordPress coding standards.

= 1.0.1 =
* Always enqueue/include jQuery in the frontend.

= 1.0.0 =
* Initial release.