=== JSON-LD Schema for Yandex Metrica ===
Contributors: antoniolite
Donate link: https://www.antoniolite.com/
Tags: yandex metrica, yandex, metrica, analytics, content analytics, content reports, schema, json
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 5.5
Stable tag: 1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Insert the needed JSON-LD Schema in your post pages so you can use the content reports in Yandex Metrica

== Description ==

Yandex Metrica is a Yandex's free tool for everyone to track your website. This plugins inserts the needed JSON-LD Schema in your post pages so you can use the content reports in Yandex Metrica.

The content reports in Yandex Metrica show you key publisher indicators in real time, the most popular articles and click-through sources, engagement indicators and separate traffic rubrics, traffic and audience interest for materials by author, engagement and traffic indicators for various topics.

You can read about the content reports at the official [Yandex Metrica Blog](https://yandex.com/blog/metrica/yandex-metrica-for-media-introducing-content-reports).

You can register with Yandex Metrica and create your tag for free in the [official web site](https://metrica.yandex.com/).

You need to insert the Yandex Metrica tracking code in your website.

Please note that you must activate the content reports in your tag's settings.

= Data included in the JSON-LD Schema =

* Post
	* Title
	* Description
	* URL
	* Featured image
	* Category
	* Tags
	* Date published
	* Date updated
* Author
	* Display name
	* URL
	* Avatar
* Publisher
	* Name
	* URL
	* Logo

About the BreadcrumbList, this is how the plugin works:

As first step, the plugin recovers all the post's categories, ordering them by parent category, so the first category or categories in the list will be them without parent category, and then by category name. If you use Yoast SEO and you set the primary category, this one category will be the category passed as ListItem with position 1 in the JSON-LD Schema. If not using Yoast SEO, the first category without parent category will be ListItem with position 1 in the JSON-LD Schema.

Once the plugin set the main category, it will check with all the rest categories if some of these is a subcategory, and the first one occurrence will be the ListItem with position 2 in the JSON-LD Schema.

== Installation ==

1. Upload `antoniolite-yandex-metrica-json-ld-schema` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings / JSON-LD Schema for Yandex Metrica and and set additional options

== Frequently Asked Questions ==

= How do I insert the Yandex Metrica tracking code in my website =

You can insert the Yandex Metrica tracking code in your website by editing your `header.php` file in your theme or using a Custom HTML tag or Custom Tag Template with Google Tag Manager.

You can also search for some plugin to do this task, but I suggest you a Custom Template for Google Tag Manager.

= How do I check that the plugin works? =

You can wait till you see the content reports in Yandex Metrica.

Or if you can't wait or if you don't see the content reports in 1-2 days, you can activate the debug mode for Yandex Metrica and check in your browser console if everything is OK.

You can also use the structured data validator from Yandex Webmaster or Google Search Console to check that your pages includes a valid JSON-LD.

= I don't see the content reports in my Yandex Metrica tag =

First you need to activate the content reports in your tag's settings, and once your website includes the JSON-LD schema in your tracked pages, you should wait for some time before to see the content reports in your sidebar menu.

= What is the best way to manage the categories? =

At the moment, Yandex Metrica only use two levels for the content reports, so only a category and subcategory are passed in the JSON-LD Schema. The best practice is to set a main category and a secondary category to the post (first level category and second level category).

= What if I use others Schema plugins? =

This plugin is optimized for sending information to Yandex Metrica the way it expects. It has not been tested with other active JSON Schema plugins but Yoast SEO.

If you use Yoast SEO, you can deactivate its Schema option from the `JSON-LD Schema for Yandex Metrica` settings, so you can continue using Yoast SEO for optimizing your contents but without its JSON-LD option.

= Is there some documentation about Yandex Metrica? =

Yes, Yandex Metrica has an excellent [documentation and support](https://yandex.com/support/metrica/) for its service.

== Screenshots ==

1. Basic settings

== Changelog ==

= 1.1 =
* Updated the BreadcrumbList element to include main category and the subcategory if it exists

= 1.0 =
* First release

== Upgrade Notice ==

Make sure you get the latest version
