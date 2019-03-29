=== Plugin Name ===
Contributors: rbhavesh
Tags: single posts, post type templates, single post templates, templatic
Requires at least: 3.5
Tested up to: 4.9
License: GPLv2 or later

== Description ==

The [Templatic](http://templatic.com/) Single Template plugin provides the ability for your theme to include " Post Templates " in much the same way you add " Page Templates ", this will allow you to choose (via a simple dropdown) which post template you want to use, on a per-post basis.

All you need to do after installing and activating the plugin is to create one or more " Post Templates " in your theme's folder, when creating or editing a post, choose the post template that you would like to use. If no post template is selected, the default template will be used. See the [Custom Post Template by Templatic](https://templatic.com/news/free-custom-post-template-wordpress-plugin-released/) for further Information.

Ever since our site launch, templatic website has always been built on top of WordPress. The site has a unique homepage, blog, club page, different kind of product galleries, and best of all, unique sales pages for different kind of products. Our site has hundreds of blog posts, product sales pages with custom fields, custom pages, specially designed product archives and many other custom built functionality.

So how do we do it on a standard WordPress install? Custom Post Template is the answer.

For the unique homepage, club page, themes, and plugin gallery and other pages, we used WordPress standard homepage.php and custom page templates, archive PHP files with some custom and dynamic code built in. No problems there. It required some clever coding but overall, it is achievable.

But how do we manage custom sales pages for all the products?

With having 80+ themes, 40+ plugins and many of them requiring unique sales page design, it becomes a challenge even to manage things in the wp-admin.

The standard way to have a unique page design is to use a page template. But we can not have standard WordPress pages like about page and hundreds of pages for a product, all mixed up. It becomes messy and chaotic to organise things.

To organise things, we built the custom taxonomy for products.

In order to keep our blog posts and pages separate, we created a new taxonomy called  Product. This helps us add all the products as a standard WordPress post and keeps it in a separate section. Chaos organised.

How do we show unique sales page templates for different products then?
If you look around, you will notice different sales page for different products. For example, sales pages for our directory theme, e-commerce theme, a portfolio theme, free WordPress theme, a plugin and other sales pages are designed and structured differently.

So how did we do this? Well, we created a custom plugin for this. A plugin that lets us create a custom post template for custom post type (or call it single post template for taxonomy) and assign it to any post in that particular taxonomy. Cool ha?
Now you can have it too. Free Custom Post Template WordPress plugin is released.

How to use this plugin?

1. Download this Free Custom Post Template WordPress Plugin from above link.
2. Connect to your WordPress dashboard (wp-admin) and navigate to Plugins >> Add New Plugin >> Upload Plugin >> Now upload the downloaded (Templatic-SingleTemplate.zip) file >> Click on  Install Now.
3. Once you install this plugin successfully, click on  Activate Plugin .
4. After activating the plugin you will need to create some Post Templates to use. In order to create a Post Template, either duplicate your default single post template, or create a new template file. Insert [this](http://snippi.com/s/25qybqq) code at the very top of the file.


Add the code as mentioned above at the top of a new file.

Now when you visit any post in your wp-admin area, you should be able to see a small post template box in the sidebar will let you select WordPress article template.

In the drop down, the template you just added will be available. Simply select it.
 
Now your post will show this new template instead of the regular WordPress post design.

Also, works with WooCommerce
Yes, if you are using WooCommerce to sell products and want to design a special product page for a specific product, its possible. This plugin will work with WooCommerce as well.

You can do wonders with it.
This is a simple plugin but the possibility is endless. If you combine custom fields with the template, you can do wonders with it. Any kind of design for any of your posts in your site is possible.

So, go give it a try and let us know how you like it.

== Installation ==

**Installation**

1. Upload the entire 'Templatic-SingleTemplate' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create Post Type Templates in your theme's folder, as described in Usage

**Usage**

After you have installed the plugin, you'll need to create some Post Templates to use. In order to create a Post Template, either duplicate your default single post template, or create a new template file. Insert the following code at the very top of the file:


<?php
/*
PostType Page Template: [Descriptive Template Name]
Description: This part is optional
*/
?>


*See Screenshot #1 for what this might look like in your code editor*

Once you have created a new Post Template, either edit or create a new post, and directly underneath the post content box, you should see a new box labeled " Post Detail Template ". Choose the Post Template you want to use and publish or update the post or any custom post type too.

*See Screenshot #2 for what this might look like on your post edit screen*


== Frequently Asked Questions ==

= How do I create new Post Type Templates? =

Please see the Usage section in Installation for details on how to create new Post Type Templates.

= How do I use the Post Type Templates I created? =

Please see the Usage section in Installation for details on how to use Post Type Templates.

= Can I include this plugin in my theme for distribution? =

Yes. Source credits for the plugin should remain intact, per GPL requirements.

= How to include or exclude template option for different post type

Yes. You can by using ' tmpl_unset_post_type ' filter as per WordPress.

== Screenshots ==
1. This is what the top of your new Post Type Templates should look like
2. This is the new box that this plugin adds to your post edit screen

== Changelog ==

= 1.0 =
* Initial Release