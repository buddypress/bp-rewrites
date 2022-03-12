# Change Log

## 1.0.0-beta2

_Requires WordPress 5.4_
_Tested up to WordPress 6.0-alpha_
_Requires BuddyPress 10.0_
_Tested up to BuddyPress 11.0-alpha_

### Description

This is the BuddyPress Rewrites feature as a plugin. Thanks to it you'll get full control on any BuddyPress URLs. Please note this plugin is still under active development: you should probably use a local/staging site to play with it 😉.

### Bug fixes

- Anticipate WordPress 6.0 changes about the WP Request to make sure BP Ajax requests are using the Rewrite API. See #31.
- Make sure BuddyPress is active before trying to change anything into the site's set up. See #29.
- Remove the BP Pages settings tab to prevent any rewrite rule errors. See #27.
- Fix `bpRewritesUI()` function call when `readyState` is not fired. See #26
- Remove superfluous parameters on `remove_filter()` calls. See #25.

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
