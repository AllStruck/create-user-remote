=== Create User Remote ===
Contributors: allstruck
Donate link: http://create-user-remote.allstruck.com/
Tags: webhook, wufoo, form, user, new, add, create, remote, webservice, web-service, hook, callback, webhooks
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.0.1

Description: Leverage WebHooks in Wufoo to create new WordPress users on form submission with a field mapping interface.

== Description ==

= Add new users with incoming WebHooks from Wufoo in WordPress =

This plugin makes it easy for anyone to connect Wufoo's excellent form building and embedding service to WordPress for the purpose of adding new users. Now you can easily map one of your Wufoo form's fields directly to user data fields, even if you have custom meta fields for users.

You may wonder at first why this would be necessary, it is easy enough to create a WordPress registration form to add new users; This plugin was created because A) It is much easier to create great looking and always functioning web forms with Wufoo than by hand in many advanced cases, B) In many situations it would be greatly helpful if account information could be created in many places at once immediately on the first client contact which is something Wufoo does very well already, and C) Aside from the many other amazing features of Wufoo gained by having all user queries submitted through Wufoo generated forms; It is nice to simply have a solid backup of user submitted contact information apart from the other third-party services.


This plugin requires PHP 5 or greater.

= Setup =

After activating the plugin a new menu under Users will appear called 'Create User Remote'. Here you can enter a Handshake Key (to protect against erroneous form entry / user creation) and map each of the currently available standard user fields or meta fields to incoming Wufoo form field names. These Wufoo field names can be acquired by first pointing the WebHooks notification in Wufoo to the postbin.org site as instructed by Wufoo (you will need to use a sample submission sent there to determine the names of the mapped fields). 

== Installation ==

Wordpress automatic installation is fully supported and recommended.

Needs PHP5.

== FAQ ==

= Where should I ask questions? =

http://create-user-remote.allstruck.com

== Screenshots ==

Not yet.

== Revisions ==

* 0.1.0. Initial beta release of 2011/04/29

== Changelog ==

= 0.1.0 =
* Initial beta release

== Upgrade Notice ==

= 0.1.0 =
* No specifics. Automatic upgrade works fine.
