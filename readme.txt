=== SEO Enforcer ===
Contributors: mainehost, godthor
Tags: SEO, WordPress SEO
Requires at least: 3.9
Tested up to: 4.2.2
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ensures that your title tag and meta description tag aren't too long and truncates them if they are. Requires WordPress SEO by Yoast.

== Description ==

SEO Enforcer is a small plugin that works with WordPress SEO by Yoast (it's required), that will truncate your title tag if it's too long, same with your meta description tag. The length they are truncated at is a setting you can configure.

There is also an option to remove H1 tags from your content. Typically your theme will show the H1 tag for your post or page title already. Having more than one H1 tag is not recommended for SEO, so this will remove any it finds in your content and replace them with an H2 tag instead.

**Plugin Requirements**

* WordPress SEO by Yoast v1.6.1+

**Note:** It may work on earlier versions of those plugins but it has not been tested.

== Installation ==

1. Upload the seo-enforcer folder to the /wp-content/plugins/ directory.
2. Activate the SEO Enforcer plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin by going to the SEO Enforcer menu that appears under the Settings menu.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Admin area.

== Changelog ==

= 1.3.1 =

Released: May 26th, 2015

* **Bug Fixes**

	* Fixed a potential bug with new installs of SEO Enforcer throwing an unserialization error.

= 1.3.0 =

Released: May 26th, 2015

* **New Features**
	
	* Image alt and title attribute checks. More info on the settings page.

* **Updates**

	* Reorganized the settings page.

* **Bug Fixes**

	* Fixed a bunch of logic regarding length checks so it should be much better about what it returns.
	* Title length will now show the saved value instead of always showing the default length.

= 1.2.3 =

Released: May 8th, 2015

* **Updates**

	* If using SEO notices then the length checks will now properly use the lengths you've defined in settings. It will also respect any exceptions you've given.
	* The SEO notices will now show the recommended lengths and how many characters in excess when it's too long.

* **Bug Fixes**
	
	* Fixed an issue where lengths would be shorter than defined if using the ... truncation method.
	* You can now turn off the SEO notices in settings. It was getting stuck on and wouldn't allow you to disable it.
	* The SEO notice check will not give an error if length is at the recommended length, only when longer than it.

= 1.2.2 =

Released: May 4th, 2015

* **New Features**

	* Link for rate & review, as well as SEO Enforcer settings, appears on the plugins page.

* **Bug Fixes**

	* Cleaned up some PHP notices about undefined variables.

= 1.2.1 =

Released: April 30th, 2015

* **Bug Fixes**

	* The title and description length checks will now properly truncate after whole words instead of in the middle of a word.

= 1.2.0 =

Released: April 21st, 2015

* **New**

	* Added an option in settings to display SEO notices on admin screens where WordPress SEO or [Shopp SEO](https://wordpress.org/plugins/shopp-seo/) are used. This will give a reminder to manually enter in the SEO fields and will also give errors when content is saved and exceeds the recommended length.

* **Updates**

	* Changed the default title length to 59 characters and description length to 156 to match with WordPress SEO's recommendations.

* **Removed**

	* Cleaned out deprecated code.

= 1.1.1 =

Released: March 31st, 2015

* **Updates**

	* The deactivation of SEO Enforcer if WordPress SEO is deactivated has been removed and instead replaced with an admin notice. The problem was that when WordPress SEO was being upgraded it would in turn deactivate SEO Enforcer. If you did not realize this then you may have had SEO Enforcer deactivated for a while, maybe even now.

* **Bug Fixes**

	* Title and description lengths could be negative in certain situations and in turn create oddities. I now check for negative values to prevent those oddities.

= 1.1.0 =

Released: March 26th, 2015

* **New**

	* Truncation exceptions for titles and descriptions.
	* Any exception list now accepts *blog* if you want your blog index to be an exception to a rule.

* **Known Issues**

	* Upgrading WordPress SEO will deactivate SEO Enforcer so you have to activate it after the upgrade. I'm hoping to fix this very soon. 

= 1.0.2 =

Released: February 19th, 2015

* Verified compatability with WordPress 4.1.1

= 1.0.1 =

Released: February 10th, 2015

* **Bug Fixes**

	* Fixed an issue where the plugin would not activate if WordPress SEO Premium was installed instead of the free version. It should now activate for either version of WordPress SEO.

= 1.0.0 =

Released: February 5th, 2015

* Initial release of the plugin.

== Upgrade Notice ==

= 1.3.0 =

New feature to check image alt and title attributes.

= 1.2.3 =

Bug fixes and improvements.