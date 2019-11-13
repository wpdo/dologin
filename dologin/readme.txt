=== DoLogin Security ===
Contributors: WPDO
Tags: Login security, GeoLocation login limit, limit login attempts, password less login
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Password less login. GeoLocation (Continent/Country/City) or IP range to limit login attempts. Support Whitelist and Blacklist. Will have free text SMS message passcode for 2nd step verification support soon.

== Description ==

Limit the number of login attempts through both the login and the auth cookies.

* Free text SMS message passcode for 2nd step verification support.

* GeoLocation (Continent/Country/City) or IP range to limit login attempts.

* Password less login link.

* Support Whitelist and Blacklist.

* GDPR compliant. With this feature turned on, all logged IPs get obfuscated (md5-hashed).

* XMLRPC gateway protection.

= How GeoLocation works =

When visitors hit the login page, this plugin will lookup the Geolocation info from API, compare the Geolocation setting (if has) with the whitelist/blacklist to decide if allow login attempts.

== Privacy ==

The online IP lookup service is provided by https://www.doapi.us. The provider's privacy policy is https://www.doapi.us/privacy.

Based on the original code from Limit Login Attemps plugin and Limit Login Attemps Reloaded plugin.

== Screenshots ==

1. Plugin Settings
2. Login Page (After sent dynamic code to mobile text message)
3. Login Page (2 times left)
4. Login Page (Too many failure)
5. Login Page (Blacklist blocked)

== Changelog ==

= 1.4 - Nov 12 2019 =
* 🍀 Password less login link.

= 1.3.5 - Oct 31 2019 =
* SMS PHP Warning fix.

= 1.3.4 - Oct 31 2019 =
* REST warning fix.

= 1.3.3 - Oct 21 2019 =
* GUI cosmetic.

= 1.3.2 - Oct 21 2019 =
* 🐞 Fixed a bub that caused not enabled SMS WP failed to login.

= 1.3.1 - Oct 20 2019 =
* PHP Notice fix.

= 1.3 - Oct 20 2019 =
* 🍀 SMS login support.

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