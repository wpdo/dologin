=== Limit Login Attempts DoLogin ===
Contributors: WPDO
Tags: Login security, GeoLocation login limit, limit login attempts
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.2.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

GeoLocation (Continent/Country/City) or IP range to limit login attempts. Support Whitelist and Blacklist. Will have free text SMS message passcode for 2nd step verification support soon.

== Description ==

Limit the number of login attempts through both the login and the auth cookies.

GeoLocation (Continent/Country/City) or IP range to limit login attempts.

Support Whitelist and Blacklist.

Will have free text SMS message passcode for 2nd step verification support soon.

GDPR compliant. With this feature turned on, all logged IPs get obfuscated (md5-hashed).

*XMLRPC* gateway protection.

= How GeoLocation works =

When visitors hit the login page, this plugin will lookup the Geolocation info from API, compare the Geolocation setting (if has) with the whitelist/blacklist to decide if allow login attempts.

== Privacy ==

The online IP lookup service is provided by https://www.doapi.us. The provider's privacy policy is https://www.doapi.us/privacy.

Based on the original code from Limit Login Attemps plugin and Limit Login Attemps Reloaded plugin.

== Screenshots ==

1. Plugin Settings
2. Login Page (2 times left)
3. Login Page (Too many failure)
4. Login Page (Blacklist blocked)

== Changelog ==

= 1.2.2 - Oct 1 2019 =
* Auto redirect to setting page after activation.

= 1.2.1 - Sep 30 2019 =
* Doc improvement.

= 1.2 - Sep 28 2019 =
* 🍀 XMLRPC protection.

= 1.1.1 - Sep 28 2019 =
* 🐞 Auto upgrade can now check latest version correctly.

= 1.1 - Sep 28 2019 =
* 🍀 *New* Display login failure log.
* 🍀 *New* GDPR compliance.
* 🍀 *New* Auto upgrade.
* *GUI* Setting link shortcut from plugin page.
* *GUI* Display security status on login page.
* 🐞 Stale settings shown after successfully saved.
* 🐞 Duration setting can now be saved correctly.
* 🐞 Fully saved geo location failure log.

= 1.0 - Sep 27 2019 =
* Initial Release.