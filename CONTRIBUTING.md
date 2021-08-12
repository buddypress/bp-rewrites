# Contributing Guidelines

## Guidelines

- As with all WordPress projects, we want to ensure a welcoming environment for everyone. With that in mind, all contributors are expected to follow our [Code of Conduct](/CODE_OF_CONDUCT.md).

- All WordPress projects are [licensed under the GPLv2+](/LICENSE.md), and all contributions to BP Rewrites will be released under the GPLv2+ license. You maintain copyright over any contribution you make, and by submitting a pull request, you are agreeing to release that contribution under the GPLv2+ license.

## Getting Started

### Testing & reporting issues

#### Getting yourself ready to test

As BP Rewrites is a BuddyPress "feature as a plugin" which goal is to replace its BP Legacy URL parser by the [WordPress Rewrite API](https://developer.wordpress.org/reference/classes/wp_rewrite/), doing as many tests as possible of the feature is very important for us all. BP Rewrites will soon be available in the WordPress.org Plugins directory to make it easier for any BuddyPress user to test it. In the meantime the easiest way to test it is to download & install the `bp-rewrites.zip` file from the **Assets** section of the [latest GitHub release](https://github.com/buddypress/bp-rewrites/releases/). Once downloaded, you can upload this `bp-rewrites.zip` to your Website from the upload tool of the Plugins > Add New Plugin WordPress Administration screen and activate it.

PS: if you don't have BuddyPress installed yet, don't forget to [install it first](https://wordpress.org/plugins/buddypress/#installation) 😉.

#### Testing

Go to the Settings > BuddyPress > URLs Administration screen and customize some slugs from there. Save your edits and start checking everything works fine with your site and specific configuration. Our main concern is making sure to maintain a backward compatibility with BuddyPress plugins and themes that are still using the legacy way to build & parse BuddyPress URLs.

#### Reporting an issue

You've found a bug! First, please make sure to prepare a detailed list of the steps to perform to reproduce this issue. Then please tell us about it reporting your issue using BP Rewrites repository [Issue Tracker](https://github.com/buddypress/bp-rewrites/issues).

### Contributing with code

BP Rewrites is a BuddyPress plugin (which is a WordPress plugin to power community sites). BuddyPress is a PHP, MySQL, and JavaScript based project, and uses Node for its JavaScript dependencies. The BuddyPress development version includes a local development environment to quickly get up and running.

First, you need to get the development version of the BuddyPress plugin. This [tutorial](https://codex.buddypress.org/participate-and-contribute/contribute-with-code/) is explaining in details how you can get it and set up some required tools.

If you feel comfortable with using GitHub.com, you simply need to clone the [BuddyPress development readonly repository GitHub is hosting there](https://github.com/buddypress/buddypress).

```bash
git clone https://github.com/buddypress/buddypress.git
```

You will need a basic understanding of how to use the command line on your computer. This will allow you to set up the local development environment, to start it and stop it when necessary, and to run the tests.

You will need Node and npm installed on your computer. Node is a JavaScript runtime used for developer tooling, and npm is the package manager included with Node. If you have a package manager installed for your operating system, setup can be as straightforward as:

* macOS: `brew install node`
* Windows: `choco install node`
* Ubuntu: `apt install nodejs npm`

If you are not using a package manager, see the [Node.js download page](https://nodejs.org/en/download/) for installers and binaries.

You will also need [Docker](https://www.docker.com/products/docker-desktop) installed and running on your computer. Docker is the virtualization software that powers the local development environment. Docker can be installed just like any other regular application.

#### Including BP Rewrites into the BuddyPress local development environment

Clone the BP Rewrites plugin on your computer and make sure to do it outside of the `buddypress` directory you created after cloning BuddyPress earlier.

```bash
git clone https://github.com/buddypress/bp-rewrites.git
```

Then move inside the `bp-rewrites` directory and install some development dependencies:

```bash
composer install
```

Then move inside the `buddypress` directory to add the following content into a `.wp-env.override.json` file:

```json
{
	"core": "WordPress/WordPress#master",
	"plugins": [ ".", "/absolute/path/to/bp-rewrites" ],
	"config": {
		"WP_DEBUG": true,
		"SCRIPT_DEBUG": true
	}
}
```

**NB: edit this file changing `/absolute/path/to` with the absolute path to the `bp-rewrites` directory you created after cloning BP Rewrites.**

#### Development Environment Commands

Ensure [Docker](https://www.docker.com/products/docker-desktop) is running before using these commands.

#### To start the development environment for the first time

```
npm install
npm run wp-env start
```

Your WordPress community site will be accessible at http://localhost:8888.

#### To stop the development environment

You can stop the environment when you're not using it to preserve your computer's power and resources:

```
npm run wp-env stop
```

#### To start the development environment again

Starting the environment again is a single command:

```
npm run wp-env start
```

#### Credentials

To login to the site, navigate to http://localhost:8888/wp-admin.

* Username: `admin`
* Password: `password`

To generate a new password (recommended):

1. Go to the Dashboard
2. Click the Users menu on the left
3. Click the Edit link below the admin user
4. Scroll down and click 'Generate password'. Either use this password (recommended) or change it, then click 'Update User'. If you use the generated password be sure to save it somewhere (password manager, etc).

#### Final installation step.

Go to the Plugins Administration screen. Deactivate BP Rewrites and Activate it again. As Plugins are automatically activated a race condition avoids BP Rewrites to make BuddyPress first adaptations (changing the directory pages post type).

#### Submitting pull requests

You're very welcome to [submit pull requests](https://github.com/buddypress/bp-rewrites/pulls). Before you do so, make sure to run these two commands 🙏:

```bash
composer phpcompat
composer do:wpcs
```

You may need to edit your code after the second commands so that it follows the [WordPress PHP Coding standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
