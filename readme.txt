=== BP Rewrites ===
Contributors: johnjamesjacoby, DJPaul, boonebgorges, r-a-y, imath, mercime, tw2113, dcavins, hnla, karmatosed, slaFFik, dimensionmedia, henrywright, netweb, offereins, espellcaste, modemlooper, danbp, Venutius, apeatling, shanebp
Donate link: https://wordpressfoundation.org
Tags: BuddyPress, rewrites, beta, feature-as-a-plugin
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.9
Requires PHP: 5.6
Tested up to: 6.3
Stable tag: 1.6.0

The BP Rewrites plugin is a BuddyPress add-on giving you the full control of any BuddyPress generated URLs.

== Description ==

**Get involved into building the BuddyPress' next way of parsing URLs!**

BP Rewrites is a BuddyPress feature as a plugin which end goal is to be merged with BuddyPress Core. It's being developed and maintained by the official BuddyPress development team. As migrating the BP Legacy URL parser to using the WP Rewrites API is potentially a breaking change for some BuddyPress plugins and themes, the BuddyPress team needs the BuddyPress users, contributors & developers to massively test this feature before safely including it into BuddyPress Core.

Please help us meeting this challenge by testing the BP Rewrites plugin with your specific configurations (Plugins, theme, bp-custom.php file, constants).

= Benefits about using the WP Rewrite API =

* All BuddyPress generated URLs are customizable.
* BuddyPress is fully compatible with plain URL permalinks.
* Parsing BuddyPress URLs is faster, more reliable, extensible, testable and fully compliant with WordPress best practices.

**PS: combined with BuddyPress 11.0, the BP Rewrites plugin is also adding a new BuddyPress Settings option to restrict the BuddyPress generated pages (aka the community area of your site) to your members only.**

= How BP Rewrites changes BuddyPress’ behavior? =

Once activated, The plugin is editing the post type of the existing BuddyPress pages in favor of the `buddypress` post type. That’s why you don’t see the BuddyPress pages anymore (as long as the BP Rewrites plugin is active) into the corresponding WordPress Administation edit screen. The BuddyPress Pages settings screen is replaced by a BuddyPress URLs settings screen (see the screenshot below). This new screen is where you can define custom slugs which will be saved as a post meta of the corresponding `buddypress` post type item. Then the BP Rewrites plugin is taking benefit from BuddyPress hooks/APIs to change BP Core’s behavior.

If you notice one of your BuddyPress plugins or your theme are not behaving the way they should, please temporarly define the `WP_DEBUG` constant to `true` into the `wp-config.php` file of your website. You should see some warning notices confirming there's an issue involving changes introduced by the BP Rewrites add-on. You can tell us about it using this plugin's support forum or best submitting an issue into the [GitHub repository](https://github.com/buddypress/bp-rewrites) of the plugin.

= How to get regular BuddyPress’ behavior back? =

Simply deactivate BP Rewrites. When you deactivate the plugin, the `buddypress` post type’s items are switched back to regular pages and you get them back into the corresponding WordPress Administation edit screen. Post metas are still there in case you want to activate BP Rewrites again (this can happen when you’re testing another BuddyPress plugin). If you absolutely want to get rid of these post metas, you can delete the BuddyPress pages, create new ones and redo the page mapping from the BuddyPress Pages settings screen.

= Join our community =

If you're interested in contributing to BuddyPress, we'd love to have you. Head over to the [BuddyPress Documentation](https://codex.buddypress.org/participate-and-contribute/) site to find out how you can pitch in.

Growing the BuddyPress community means better software for everyone!

== Installation ==

= Requirements =

* WordPress 5.9.
* BuddyPress 10.0 or 11.0 (Most of this plugin has been merged into BuddyPress 12.0).

= Automatic installation =

Using the automatic installation let WordPress handles everything itself. To do an automatic install of BP Rewrites, log in to your WordPress dashboard, navigate to the Plugins menu. Click on the Add New link, then activate the "BuddyPress Add-ons" tab to quickly find the BP Rewrites plugin's card.
Once you've found the BP Rewrites, you can view details about the latest release, such as community reviews, ratings, and description. Install the BP Rewrites add-on by simply pressing "Install Now".

== Frequently Asked Questions ==

= Where can I get support? =

Our community provides free support at [https://buddypress.org/support/](https://buddypress.org/support/).

= Where can I report a bug? =

Report bugs or suggest ideas at [https://github.com/buddypress/bp-rewrites/issues](https://github.com/buddypress/bp-rewrites/issues), participate to this plugin development at [https://github.com/buddypress/bp-rewrites/pulls](https://github.com/buddypress/bp-rewrites/pulls).

= Who builds the BP Rewrites add-on? =

The BP Rewrites is a BuddyPress add-on and is free software, built by an international community of volunteers. Some contributors to BuddyPress are employed by companies that use BuddyPress, while others are consultants who offer BuddyPress-related services for hire. No one is paid by the BuddyPress project for his or her contributions.

If you would like to provide monetary support to the BP Rewrites or BuddyPress plugins, please consider a donation to the [WordPress Foundation](https://wordpressfoundation.org), or ask your favorite contributor how they prefer to have their efforts rewarded.

== Screenshots ==

1. **The BuddyPress URLs admin screen**
2. **The community visibility option**

== Upgrade Notice ==

= 1.6.0 =

Maintenance release. No specific upgrade routines are performed.

= 1.5.0 =

Feature release. No specific upgrade routines are performed.

= 1.4.0 =

Maintenance release. No specific upgrade routines are performed.

= 1.3.0 =

Maintenance release. No specific upgrade routines are performed.

= 1.2.0 =

Maintenance release. No specific upgrade routines are performed.

= 1.1.0 =

Maintenance release. No specific upgrade routines are performed.

= 1.0.1 =

Initial version of the plugin, no upgrade needed.

== Changelog ==

= 1.6.0 =

- Interrupt the plugin loading process if BuddyPress is >= 12.0.0
- Bump WordPress "Tested up to" 6.3
- Make sure directory rewrite rules match exact directory slugs
- Only check if a BP directory is set as the site homepage if the requested URL is this homepage.
- `Core_Nav_Compat`: only send warning notices in right contexts.

= 1.5.0 =

- Introduce a visibility feature to restrict community pages’ access to members only.

= 1.4.0 =

- Prevents a fatal error when trying to edit a nav too early.
- Adds BP Specific exceptions to the "was called too early" notices.

= 1.3.0 =

- Do not trigger "was called too early" notices for other WordPress specific contexts: signup, activation, comment submission and trackback management.
- Bring BP Rewrites support to bbPress.

= 1.2.0 =

- Make sure the primary nav is set before resetting it.
- Make sure the Global Notices link is reset for BP Nouveau.

= 1.1.0 =

- Make sure Members Invitations URLs are handled.
- Rewrite the Member's header private message link.

= 1.0.1 =

Initial version of the plugin.
