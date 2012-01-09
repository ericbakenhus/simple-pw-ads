=== Simple PW Ads ===
Contributors: Big Bagel
Donate link: http://interruptedreality.com/plugins/simple-pw-ads/
Tags: ad, ads, advertising, Project Wonderful, widget, shortcode, template tag
Requires at least: 3.2
Tested up to: 3.3
Stable tag: 3.0.2

Provides easy ways to place Project Wonderful ads on your site.

== Description ==

Provides three (widget, shortcode, and template tag) easy ways to add Project Wonderful ads to your WordPress site along with a simple management page to handle them.

This plugin was intentionally kept simple. Insert your Project Wonderful publisher ID/member number on the management page, click "Synchronize Ad Data", and watch as all your ad information is displayed. You can then use the added widget, shortcode, or template tag to display any of the ads connected to your PW account.

See the [Other Notes](http://wordpress.org/extend/plugins/simple-pw-ads/other_notes/) page for the shortcode and template tag as well as usage notes.

**Before marking this plugin as broken**, ask for some help in [the forum](http://wordpress.org/tags/simple-pw-ads?forum_id=10#postform) or on the [author's site](http://interruptedreality.com/plugins/simple-pw-ads/). It could be a minor bug with a simple fix specific to your configuration.

= New in version 3 =

**Important**:
Before version 3.0.0 there was a very serious bug. The Project Wonderful asynchronous code was added to the footer in such a way that it could confuse the Project Wonderful robot in charge of checking ads. As a result, ads could have difficulty activating or be automatically deactivated. Do not use any version of this plugin prior to 3.0.0.  

Simple PW Ads now includes a management page to keep track of your ads. Any ads added in versions prior to 3.0.0 will need to be checked/re-added once you input your publisher ID/member number.

Plugin Website: [Simple PW Ads](http://interruptedreality.com/plugins/simple-pw-ads/)

== Installation ==

You can use the "Install Plugins" page in your dashboard to search for and automatically install the plugin.

If you need or want to manually install the plugin:

1. Download and extract Simple PW Ads
2. Upload the `simple-pw-ads` folder to the `/wp-content/plugins/` directory
3. Activate/Network activate the plugin through the 'Plugins' menu in WordPress

If you receive any errors, you can ask about them in [the forum](http://wordpress.org/tags/simple-pw-ads?forum_id=10#postform) or on the [author's site](http://interruptedreality.com/plugins/simple-pw-ads/).

== Frequently Asked Questions ==

None yet!

This plugin is actively maintained and will remain so until the universe implodes. Create a topic in [the forum](http://wordpress.org/tags/simple-pw-ads?forum_id=10#postform) or on the [author's site](http://interruptedreality.com/plugins/simple-pw-ads/) if you have any problems, questions, or suggestions.

== Screenshots ==

1. The added widget.
2. The management page.

== Changelog ==

= 3.0.2 =
* Minor bug squashing; accidentally used "<?" instead of "<?php" in one spot.
* Tested with latest 3.3 beta.

= 3.0.1 =
* Added several sanity checks and more detailed error reporting.
* Switched to WordPress' HTTP API to make fetching XML from Project Wonderful more robust.
* Fixed a logic error: In 3.0.0 deleting an ad box in Project Wonderful would make every subsequent ad number decrease by one. Now, ad numbers are immutable and, once associated with an ad, will never change.

= 3.0.0 =
* Now uses Project Wonderful's xmlpublisherdata.php to retrieve all ad information with only a publisher ID/member number.
* Allows for the use of standard and advanced code in normal or asynchronous mode.
* General bug squashing.
* Fixes a major bug in the way the asynchronous Project Wonderful code was added to the footer which could prevent new ads from being activated and cause current ads to be deactivated. Do not use any version of Simple PW Ads prior to this one.
* Due to the previous bug, this version was developed and released extremely quickly. Please report any bugs.

= 2.0.0 =
* Added management page.
* Updated widget, shortcode, and template tag to use ads added through management page.
* Replaced any uses of `split()` with `explode()` since `split()` is now deprecated.

= 1.1.1 =
* Added internationalization support.

= 1.1 =
* Made the code nicer. Nothing else.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 3.0.2 =
Very minor update. Update at your leisure.

= 3.0.1 =
Minor, but important, update. Added several sanity checks and improved error feedback, switched to native WordPress HTTP API, and made ad numbers immutable.

= 3.0.0 =
IMPORTANT: A very serious bug was found; update immediately. This is a major upgrade. Your current widgets will switch to default settings. Please go to the plugin page for more details.

= 2.0.0 =
Added management page and updated widget, shortcode, and template tag to use it. Your current ads shouldn't be affected.

= 1.1.1 =
Added internationalization. No other changes.

= 1.1 =
Made the code nicer to look at. Upgrade at your leisure.

= 1.0 =
First release.

== Notes ==
This plugin relies on PHP's SimpleXML extension. SimpleXML is enabled by default since PHP 5.1.2 (WordPress 3.2 and greater requires PHP 5.2.4). Simple PW Ads will check for SimpleXML and display a helpful error if not found.

== Planned Improvements ==

= 3.1.0 =
* ETA: Nov 1st, 2011
* Inject ads into RSS feeds.
* Allow scheduling automatic checking for updated ads.

== Usage ==

Simple PW Ads no longer requires you to mess with ad codes. Simply go to Project Wonderful and find your publisher ID/member number under "My account > My profile". Type your ID on the management page, and click "Synchronize Ad Data". The information for all of your ads will be downloaded and shown. You can now use the widget, shortcode, or template tag to insert your ads.

The added shortcode is: `[spw_ad managed_ad="X"]`  
The added template tag is: `spw_insert_ad( 'X' )`

X corresponds to "Ad Number" on the management page.

If you edit your ads within Project Wonderful, you should click "Synchronize Ad Data" again to grab the changes. If you delete an ad in Project Wonderful, all your ad numbers will remain the same; you will not have to check/edit any currently used ads.