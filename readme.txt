=== BP Rewrites ===
Contributors: johnjamesjacoby, DJPaul, boonebgorges, r-a-y, imath, mercime, tw2113, dcavins, hnla, karmatosed, slaFFik, dimensionmedia, henrywright, netweb, offereins, espellcaste, modemlooper, danbp, Venutius, apeatling, shanebp
Donate link: https://wordpressfoundation.org
Tags: BuddyPress, rewrites, beta, feature-as-a-plugin
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.9
Requires PHP: 5.6
Tested up to: 5.9
Stable tag: 1.0.0

The BP Rewrites plugin is a BuddyPress add-on using the WP Rewrite API to parse BuddyPress generated URLs.

== Description ==

**Get involved into building the BuddyPress' next way of parsing URLs!**

BP Rewrites is a BuddyPress feature as a plugin which end goal is to be merged with BuddyPress Core. It's being developed and maintained by the official BuddyPress development team. As migrating the BP Legacy URL parser to using the WP Rewrites API is potentially a breaking change for some BuddyPress plugins and themes, the BuddyPress team needs the BuddyPress users, contributors & developers to massively test this feature before safely including it into BuddyPress Core.

Please help us meeting this challenge by testing the BP Rewrites plugin with your specific configurations (Plugins, theme, bp-custom.php file, constants).

Once activated, The plugin is editing the post type of the existing BuddyPress pages in favor of the `buddypress` post type. That’s why you don’t see the BuddyPress pages anymore (as long as the BP Rewrites plugin is active) into the corresponding WordPress Administation edit screen. The BuddyPress Pages settings screen is replaced by a BuddyPress URLs settings screen (see the screenshot below). This new screen is where you can define custom slugs which will be saved as a post meta of the corresponding `buddypress` post type item. Then the BP Rewrites plugin is taking benefit from BuddyPress hooks/APIs to change BP Core’s behavior.

If you notice one of your BuddyPress plugin or your theme are not behaving the way they should, please temporarly define the `WP_DEBUG` constant to `true` into the `wp-config.php` file of your website. You should see some warning notices confirming there's an issue. You can tell us about it using this plugin's support forum or best submitting an issue into the [GitHub repository](https://github.com/buddypress/bp-rewrites) of the plugin.

When you deactivate the plugin, the `buddypress` post type’s items are switched back to regular pages and you get them back into the corresponding WordPress Administation edit screen. Post metas are still there in case you want to activate BP Rewrites back (this can happen when you’re testing another BuddyPress plugin). If you absolutely want to get rid of these post metas, you can delete the BuddyPress pages, create new ones and redo the page mapping from the BuddyPress Pages settings screen.

= Join our community =

If you're interested in contributing to BuddyPress, we'd love to have you. Head over to the [BuddyPress Documentation](https://codex.buddypress.org/participate-and-contribute/) site to find out how you can pitch in.

Growing the BuddyPress community means better software for everyone!

== Installation ==

= Requirements =

* WordPress 5.9.
* BuddyPress 10.0

= Automatic installation =

Using the automatic installation let WordPress handles everything itself. To do an automatic install of BP Rewrites, log in to your WordPress dashboard, navigate to the Plugins menu. Click on the Add New link, then activate the "BuddyPress Add-ons" tab to quickly find the BP Rewrites plugin's card.
Once you've found the BP Rewrites, you can view details about the latest release, such as community reviews, ratings, and description. Install the BP Search Block by simply pressing "Install Now".

== Frequently Asked Questions ==

= Where can I get support? =

Our community provides free support at [https://buddypress.org/support/](https://buddypress.org/support/).

= Where can I report a bug? =

Report bugs or suggest ideas at [https://github.com/buddypress/bp-rewrites/issues](https://github.com/buddypress/bp-rewrites/issues), participate to this plugin development at [https://github.com/buddypress/bp-rewrites/pulls](https://github.com/buddypress/bp-rewrites/pulls).

= Who builds the BP Rewrites? =

The BP Rewrites is a BuddyPress add-on and is free software, built by an international community of volunteers. Some contributors to BuddyPress are employed by companies that use BuddyPress, while others are consultants who offer BuddyPress-related services for hire. No one is paid by the BuddyPress project for his or her contributions.

If you would like to provide monetary support to the BP Rewrites or BuddyPress plugins, please consider a donation to the <a href="https://wordpressfoundation.org">WordPress Foundation</a>, or ask your favorite contributor how they prefer to have their efforts rewarded.

== Screenshots ==

1. **The BuddyPress URLs admin screen**

== Upgrade Notice ==

= 1.0.0 =
Initial version of the plugin, no upgrade needed.

== Changelog ==

= 1.0.0 =
Initial version of the plugin.
