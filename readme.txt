=== Social Manager by NineCodes ===
Contributors: ninecodes, tfirdaus, hongkiat
Tags: widget, json, wp-api, social-media, sharing, facebook, twitter, pinterest, open-graph, twitter-cards
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Optimize your website presence in social media.

== Description ==

Social Manager optimize your website in social media with [Open Graph](http://ogp.me/) and [Twitter Cards](https://dev.twitter.com/cards/overview) meta tags to your posts and pages. These meta tags help social media, like Facebook and Twitter, to understand your content structure better as well as enable them to render the content on the [News Feed](https://www.facebook.com/help/327131014036297/) nicely. This plugin allows you to customize the meta tags, so you have more control over your posts and pages presentation on these social media.

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
- PHP 5.3 or above
- WordPress 4.5 or above

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
2. This plugin registers a custom route at `/ninecodes/v1/social-manager/buttons`.

= For Theme Developers: =

If you are a Theme developer, you can add `add_theme_support( 'ninecodes-social-manager' )` in `functions.php` of your theme themes to customize the plugin at Theme level. The following are the "features" that we currently support.

**Remove the plugin stylesheet**

Set the `stylesheet` to `false` will dequeue the plugin stylesheet. This allows you to customize the the plugin output through your theme stylesheet to match your theme design as a whole without having to do an override.

<pre lang="php">add_theme_support( 'ninecodes-social-manager', array(
	'stylesheet' => false,
) );</pre>

**Custom attribute prefix**

The plugin add prefix `ninecodes-social-manager` to (almost) any HTML elements it outputs at the front-end (your theme), for example:

<pre lang="html">&lt;div class=&quot;ninecodes-social-manager-buttons ninecodes-social-manager-buttons--content ninecodes-social-manager-buttons--content-after&quot; id=&quot;ninecodes-social-manager-buttons-1241&quot;&gt;&lt;div class=&quot;ninecodes-social-manager-buttons__list ninecodes-social-manager-buttons__list--icon&quot; data-social-buttons=&quot;content&quot;&gt;
&lt;/div&gt;</pre>

Don't like it? You can change this prefix to anything you prefer by adding the `attr-prefix`, for example:

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

= 1.0.1 =
* Added: screenshot images.
* Fixed: code block formatting in the `readme.txt`.
* Fixed: endpoint address stated in the "Installation" section of `readme.txt`.
* Fixed: the use of `$this` keyword in the metabox required files path.
* Changed: the JavaScript function to compile Underscore template.
* Changed: call Backbone Model `.fetch()` method after the Views are already instantiated.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
* Added screenshot images, fixed a number of bugs and error formatting in `readme.txt` file, and a few improvements in the JavaScript.

= 1.0.0 =
* Initial release.
