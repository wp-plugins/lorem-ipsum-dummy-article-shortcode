=== Lorem ipsum dummy article shortcode ===
Contributors: Sjeiti
Tags: lorem ipsum, dummy text, dummy html, dummy article, dummy image, shortcode
Requires at least: 2.8.6
Tested up to: 4.1
1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A seeded dummy article generator shortcode for WordPress. This does not just generate 'lorem ipsum' but full HTML articles.

== Description ==

This shortcode plugin does not simply generate dummy text but tries to mimic what a Wordpress page or post might look like in real life. This includes paragraphs, commas, headings, hyperlinks and images. The output is easily adjustable through the shortcode attributes.
The generated code is also **seeded**, meaning that the dummy html on page-x will always look the same (unless you specify your own seed).
The plugin also adds the shortcode filter to the title, so you can also use [lll h] in your post title.

= Shortcode attributes =

You can adjust the shortcode by adding the following attributes. Some attributes have a single character alias for quick use.

* paragraph (alias p): Number of paragraphs. Defaults to 12.
* sentence (alias s): Number of sentences per paragraph. Defaults to 5.
* word (alias w): Number of words per sentence. Defaults to 12.
* header (alias h): Indicates a header. If attribute is set the other attributes will be ignored. Defaults to null.
* seed: The random seed used for generating the article. Defaults to the post ID.
* deviation: The random deviation used to determine the number of paragraphs, sentences and words. Defaults to 0.5.

= Usage =

To use the shortcode simply add `[lll]` to a post or page.
Attributes are used like so; `[lll p=8 seed=76432]`.
When used as a post title make sure to add the 'header' attribute; `[lll h]`.


== Installation ==

Either use Wordpress' built-in plugin installer.
Or install manually; download and unzip (or clone) the plugin to the /wp-content/plugins/ directory. Then activate the plugin through the Plugins menu in WordPress.


== Versioning and issues ==

The main CVS repo for this plugin is on Github. The version up on Wordpress is a distilled build of the major tags.
If you have any issues or suggestions please put them on [Github](https://github.com/Sjeiti/Lorem-ipsum-dummy-article-shortcode/issues).


== Changelog ==
= 1.0.2 =
* initial release