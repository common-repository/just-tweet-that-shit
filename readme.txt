=== Plugin Name ===
Contributors: marcelpauly
Tags: twitter
Requires at least: 2.3
Tested up to: 3.0
Stable tag: 0.2

This Plugin connects WordPress with your Twitter account: When you publish a new article it informs your followers with a shorten link.

== Description ==

This Plugin connects WordPress with your Twitter account: When you publish a new article **Just Tweet That Shit** informs your followers with a shorten link.

There are so many WordPress plugins connecting your blog with your Twitter account. But either they are overcharged with meaningless features you don't need or they are programmed meanly or they don't support the new Twitter authorization via [OAuth](http://blog.twitter.com/2010/06/switching-to-oauth.html). **Just Tweet That Shit** is an intelligent and slim alternative.

**Features:**

* Informs your followers on Twitter about new blog articles
* URL shortening: [bit.ly](http://bit.ly/), [j.mp](http://j.mp/), [tinyurl.com](http://tinyurl.com/), [twiturl.de](http://twiturl.de/), [is.gd](http://is.gd/)
* You can convert your tags and categories to Twitter Hashtags
* Secure authorization via OAuth
* That's it - no meaningless features

**Requirements:**

* WordPress: version 2.3 or higher
* activated PHP functions: cURL and OpenSSL

== Installation ==

1. Upload the `just-tweet-that-shit` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the *Plugins* menu in WordPress.
1. Visit *Settings -> Just Tweet That Shit*.
1. In the upper section you can define how the automatic tweets should look like and choose any URL shortening service. If you coose [bit.ly](http://bit.ly/) or [j.mp](http://j.mp/) you have to enter your *bit.ly* username and API Key.
1. In the lower section you have to follow the documented steps to connect your blog with your Twitter account.
1. That's it.

== Frequently Asked Questions ==

= What I have to enter into the fields of the Twitter application you didn't describe? =

Leave them blank respectively dont's change them.

= I clicked on the "Sign in with Twitter" button but the announced page isn't displayed. What happened? =

* Either your Server doesn't support cURL and OpenSSL. This support is required.
* Or something denies the browser access on the PHP files in the plugin directory, a `.htaccess` file for example. Allow the browser access. (After the connection is established you can redeny it.)

= I just published a post but there's no tweet. What happened? =

There are a lot of possible error sources:

* You need WordPress version 2.3 or higher.
* Your server have to support cURL and OpenSSL.
* You didn't choose a URL shortener and posted a article with an URL which exeed the 140 charecters limit.
* When you published your article Twitter was "over capacity".
* You used QuickPress on your WordPress Dashboard - this function isn't supported currently.
* The plugin doesn't tweet an article, if it is password protected.

= Why there's no URL in my Tweet? =

Your Server deactivated the PHP function "file_get_contents". That's not a problem as long as you use WordPress version 2.7 or higher. So update your WordPress.

= I choosed an URL shortener. Why the URL isn't shorten? =

* Either the URL shortening service wasn't available when you published your article.
* Or you choosed "bit.ly" or "j.mp" but forgot to enter your *bit.ly* username and API Key.

== Screenshots ==

1. You can configure how the automatic tweets should look like and which URL shortening service is used
2. You have to register an own Twitter Application for your blog
3. Your Twitter Application provides "Consumer key" and "Consumer secret"
4. Twitter will ask you to allow the access ...
5. ... after confirmation the connection is established
6. Very cool: your automatic tweets will have their own signature

== Changelog ==

= 0.2 =
* You can convert your tags and categories to Twitter Hashtags
* Supporting URL shortening with deactivated PHP function `file_get_contents`
* Better handling with some user mistakes
* FAQ

= 0.1 =
* First version.

== Upgrade Notice ==

= 0.2 =
First revision with some betterments and a new feature: now you can convert your tags and categories to Twitter Hashtags

= 0.1 =
First version.
