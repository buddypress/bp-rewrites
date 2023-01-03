# Change Log

## 1.5.0

_Requires WordPress 5.9_
_Tested up to WordPress 6.1_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-RC1_

### Description

BP Rewrites feature as a plugin maintenance release, please upgrade. With BP Rewrites activated, you'll get full control on any BuddyPress URLs.

### Changes

- Introduce a visibility feature to restrict community pages’ access to members only. See [#44](https://github.com/buddypress/bp-rewrites/pull/44).

## Props

@imath, @dcavins.

---

## 1.4.0

_Requires WordPress 5.9_
_Tested up to WordPress 6.1_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

BP Rewrites feature as a plugin maintenance release, please upgrade. With BP Rewrites activated, you'll get full control on any BuddyPress URLs.

### Changes

- Prevents a fatal error when trying to edit a nav too early. See [#42](https://github.com/buddypress/bp-rewrites/pull/42).
- Adds BP Specific exceptions to the "was called too early" notices. See [#43](https://github.com/buddypress/bp-rewrites/pull/43).

## Props

@imath.

---

## 1.3.0

_Requires WordPress 5.4_
_Tested up to WordPress 6.0_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

BP Rewrites feature as a plugin maintenance release, please upgrade. With BP Rewrites activated, you'll get full control on any BuddyPress URLs.

### Changes

- Do not trigger "was called too early" notices when submitting comments. See [#40](https://github.com/buddypress/bp-rewrites/pull/40).
- Do not trigger "was called too early" notices for other WordPress specific contexts: signup, activation and trackbacks management.
- Bring BP Rewrites support to bbPress. See [#41](https://github.com/buddypress/bp-rewrites/pull/41).

## Props

@shawfactor & @imath.

---

## 1.2.0

_Requires WordPress 5.4_
_Tested up to WordPress 6.0_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

BP Rewrites feature as a plugin maintenance release, please upgrade. With BP Rewrites activated, you'll get full control on any BuddyPress URLs.

### Changes

- Make sure the primary nav is set before resetting it. See [#39](https://github.com/buddypress/bp-rewrites/pull/39).
- Make sure the Global Notices link is reset for BP Nouveau.

## Props

@shawfactor & @imath.

---

## 1.1.0

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-beta3_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the first maintenance release of the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs.

### Changes

- Make sure Members Invitations URLs are handled. See [#37](https://github.com/buddypress/bp-rewrites/pull/37).
- Rewrite the Member's header private message link.

## Props

@shawfactor & @imath.

---

## 1.0.1

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-beta2_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the first security release of the BuddyPress Rewrites feature as a plugin. It applies the security fixes recommanded into the review of WordPress.org Plugins team. Please upgrade asap.

### Changes

- Sanitize, escape, and validate $_POST, $_GET, $_REQUEST and $_FILE missed calls in the plugin. See [#36](https://github.com/buddypress/bp-rewrites/pull/36).

### Props

The [WP Plugin review team](https://make.wordpress.org/plugins/handbook/the-team/) & @imath.

---

## 1.0.0

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-beta1_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the first stable version of the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs.

### Changes

- Create the `readme.txt` used by the WordPress.org plugin directory.
- Submit the request about hosting the plugin on this directory.

### Props

@adiloztaser, @eha1, @shanebp, @boonebgorges, @r-a-y, @imath.

---

## 1.0.0-RC1

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-alpha_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs. Please note this plugin is still under active development: you should probably use a local/staging site to play with it 😉.

### Bug fixes

- Make sure BP Rewrites loads when BuddyPress is network activated. See [#32](https://github.com/buddypress/bp-rewrites/pull/32).
- Avoid a fatar error when trying to access to a BP Core Nav too early. See [#33](https://github.com/buddypress/bp-rewrites/pull/33).

### Props

@adiloztaser, @eha1, @imath.

---

## 1.0.0-beta2

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-alpha_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs. Please note this plugin is still under active development: you should probably use a local/staging site to play with it 😉.

### Bug fixes

- Anticipate WordPress 6.0 changes about the WP Request to make sure BP Ajax requests are using the Rewrite API. See [#31](https://github.com/buddypress/bp-rewrites/pull/31).
- Make sure BuddyPress is active before trying to change anything into the site's set up. See [#29](https://github.com/buddypress/bp-rewrites/pull/29).
- Remove the BP Pages settings tab to prevent any rewrite rule errors. See [#27](https://github.com/buddypress/bp-rewrites/pull/27).
- Fix `bpRewritesUI()` function call when `readyState` is not fired. See [#26](https://github.com/buddypress/bp-rewrites/pull/26)
- Remove superfluous parameters on `remove_filter()` calls. See [#25](https://github.com/buddypress/bp-rewrites/pull/25).

### Props

@adiloztaser, @imath.

---

## 1.0.0-beta1

_Requires WordPress 5.4_
_Tested up to WordPress 5.9_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 10.0_

### Description

This is the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs. Please note this plugin is still under active development: you should probably use a local/staging site to play with it 😉.

### Props

@shanebp, @boonebgorges, @r-a-y, @imath.
