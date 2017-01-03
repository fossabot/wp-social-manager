=== Social Manager ===
Contributors: ninecodes, tfirdaus, hongkiat
Tags: widget, json, wp-api, social-media, sharing, facebook, twitter, pinterest, open-graph, twitter-cards
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helps optimizing your website in social media.

== Description ==

This plugin helps you optimize your website in social media with [Open Graph](http://ogp.me/) and [Twitter Cards](https://dev.twitter.com/cards/overview) meta tags. Facebook, Twitter, and other social media sites will scan these meta tags to understand your content structure better as well as enable them to render the content preview on their [News Feed](https://www.facebook.com/help/327131014036297/) properly. This plugin allows you to customize the meta tags, so you have more control over your posts and pages presentation.

Other features included in the plugin:

= Social Buttons =
Display social buttons to allow your readers sharing your website posts and pages to Facebook, Twitter, Pinterest, LinkedIn, Reddit, etc. You can also display the social buttons on the images within the content.

With the increasing trend on using WordPress as a [*headless* CMS](https://2016.sydney.wordcamp.org/session/using-wordpress-as-a-headless-cms/), this plugin also exposes a couple of custom **JSON API** routes using the [WP-API](http://v2.wp-api.org/) infrastructure in WordPress. The API allows developers to retrieve the social media sharing endpoint URLs of a particular post or page and render the sharing buttons in, for example, a JavaScript-based theme.

= Social Profiles =
With this plugin you can add your social profile and page URLs and display them on a widget. No need to mess around with your theme Menu ever again; the widget will stay there even when you've changed the theme.

= Hooks =
For developers, you can utilize the Action and Filter Hooks to customize the plugin. See the "Installation" tab for a few examples, and dig the source code for more.

= Translations =
- English
- Indonesia

Translate this plugin to your language on [__translate.wordpress.org__](https://translate.wordpress.org/projects/wp-plugins/ninecodes-social-manager).

= Requirements =
- PHP 5.3 or higher
- WordPress 4.5 or higher

== Installation ==

= WordPress Plugins Directory (Recommended): =

1. Visit **Plugins > Add New**
2. Search for **Social Manager by NineCodes**
3. Activate **Social Manager by NineCodes** from the Plugins page.

= Manual Upload: =

1. Download the plugin `.zip` archive.
2. Visit **Plugins > Add New**.
3. Click **Upload Plugin**
4. Click the **Choose File** button, and select the plugin `.zip` archive you have just downloaded.
6. Click the **Install Now** button.
3. Activate **Social Manager by NineCodes** from the Plugins page.

= FTP Upload: =

If none of the above ways work, though it will be less convenient, you can try installing the plugin via FTP (File Transfer Protocol). To do this, you will need an FTP software installed on your computer, such as:

* [FileZilla](https://filezilla-project.org/) (Windows, macOS, Linux)
* [CyberDuck](https://cyberduck.io/) (Windows, macOS)
* [CarotDAV](http://rei.to/carotdav_en.html) (Windows)

Then, login to your server *with the credentials given by your hosting provider*.

1. Download the plugin `.zip` archive.
2. Unzip the archive and upload the `ninecodes-social-manager` folder into the plugin folder (`/wp-content/plugins/`).
3. Activate **Social Manager by NineCodes** from the Plugins page.

= Once Activated: =

1. This plugin adds a new setting page named **Social** under the **Settings** menu in the WordPress admin screen. You can customize the output made by the plugin through this page.
2. This plugin also adds some extra fields in the user profile edit screen (`/wp-admin/profile.php`).
3. This plugin registers a custom route at `/ninecodes/v1/social-manager/buttons`.

== Theme Support ==

If you are a Theme developer, you can add `add_theme_support( 'ninecodes-social-manager' )` in `functions.php` of your theme themes to customize the plugin at Theme level. The following are the "features" that we currently support.

**Remove the plugin stylesheet**

Set the `stylesheet` to `false` will dequeue the plugin stylesheet. This allows you to customize the the plugin output through your theme stylesheet to match your theme design as a whole without having to do an override.

<pre lang="php">add_theme_support( 'ninecodes-social-manager', array(
	'stylesheet' => false,
) );</pre>

**Custom attribute prefix**

The plugin add prefix `ninecodes-social-manager` to (almost) any HTML elements it outputs at the front-end (your theme). If you don't like it, you can change this prefix to anything you prefer by adding the `attr-prefix`, for example:

<pre lang="php">add_theme_support( 'ninecodes-social-manager', array(
	'attr-prefix' => 'social',
) );</pre>

Keep in mind setting the prefix to other than 'ninecodes-social-manager' will also dequeue the stylesheet much like setting the `stylesheet` to false; you will have to add the styles on your own.

**Changing the Buttons Mode**

The plugin offers 2 modes, `HTML` and `JSON`, to generate, what called as the **Social Buttons**; the buttons that allow your site users to share content on social media. By default the mode is set to `HTML`, which will *echo* all the HTML markup in the post content. But, if you are building a [*headless* WordPress theme](https://pantheon.io/decoupled-cms) using whatever JavaScript renders (Backbone, Angular, React, Vue, Ember, you name it), you might want to switch the plugin to the `JSON` mode.

<pre lang="php">add_theme_support( 'ninecodes-social-manager', array(
	'buttons-mode' => 'json',
) );</pre>

== Frequently Asked Questions ==

None, at the moment. Please ask. :)

== Screenshots ==

1. The plugin settings page.
2. The plugin custom meta box to enable or disable social buttons on the content and images.
3. Social media profile widget settings.
4. Social media profile widget view on the front-end.
5. Social buttons to share the content.
6. Social buttons to share the content image.

== Changelog ==

= [1.0.4] - 2017-01-03 =
* __Changed__: render the icons before anything else, which should allow the icons to render ASAP without being blocked by slow JavaScript files.
* __Changed__: remove enclosed slash for Twitter Cards meta tags.
* __Changed__: incorrect file header.
* __Changed__: move the `add_theme_support` usage instruction to "Theme Support" section under "Other Notes" tab.
* __Changed__: the "END:" comment tags for consistency.
* __Changed__: the button endpoint URLs should be under ‘endpoints’ object, and the Underscore.js template following this change.
* __Fixed__: adjust the icon size and padding ensuring the icon is perfectly aligned to the center.


= [1.0.3] - 2017-01-02 =
* __Added__: [Open Graph Protocol Tools](https://github.com/niallkennedy/open-graph-protocol-tools) library files.
* __Changed__: use Open Graph Protocol Tools methods to generate Open Graph meta tags for validation and standardization.
* __Changed__: use double quotes instead of single quote to wrap up Twitter Cards meta tags value following meta tags generated through [Open Graph Protocol Tools](https://github.com/niallkennedy/open-graph-protocol-tools).
* __Changed__: shorten the plugin name (drop "by NineCodes").


= [1.0.2] - 2017-01-01 =
* __Changed__: namespacing Backbone application
* __Changed__: set dependency of the `preview-profile.js` to just `backbone`. The `backbone` will also enqueue `jquery` and `underscore`.
* __Changed__: feed to News Feed (Facebook)
* __Changed__: update "Tested up to" to 4.7.
* __Changed__: transform HTML markup in `readme.txt` to its entity (also fixed wp.org render HTML code block issue).
* __Changed__: remove `edit_user_profile_update` duplicate action


= [1.0.1] - 2017-01-01 =
* __Added__: screenshot images.
* __Fixed__: code block formatting in the `readme.txt`.
* __Fixed__: endpoint address stated in the "Installation" section of `readme.txt`.
* __Fixed__: the use of `$this` keyword in the metabox required files path.
* __Changed__: the JavaScript function to compile Underscore template.
* __Changed__: call Backbone Model `.fetch()` method after the Views are already instantiated.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.4 =
* A few minor bug fixes, and changes in to retain consistency.

= 1.0.3 =
* A few minor changes in the generated meta tags.

= 1.0.2 =
* A few minor bug fixes, tweaks in Backbone application, and fixed `readme.txt` formatting issue.

= 1.0.1 =
* Added screenshot images, fixed a number of bugs and error formatting in `readme.txt` file, and a few improvements in the JavaScript.

= 1.0.0 =
* Initial release.
