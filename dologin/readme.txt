=== Limit Login Attempts DoLogin ===
Contributors: WPDO
Tags: Login security, GeoLocation login limit, limit login attempts
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

GeoLocation (Continent/Country/City) or IP range to limit login attempts. Support Whitelist and Blacklist. Will have free text SMS message passcode for 2nd step verification support soon.

== Description ==

GeoLocation (Continent/Country/City) or IP range to limit login attempts. Support Whitelist and Blacklist. Will have free text SMS message passcode for 2nd step verification support soon.

GDPR compliant. With this feature turned on, all logged IPs get obfuscated (md5-hashed).

= How GeoLocation works =

When visitors hit the login page, this plugin will lookup the Geolocation info from API, compare the Geolocation setting (if has) with the whitelist/blacklist to decide if allow login attempts.

== Privacy ==

The online IP lookup service is provided by https://www.doapi.us. The provider's privacy policy is https://www.doapi.us/privacy.

== Changelog ==

= 1.1 - Sep 28 2019 =
* 🍀 *New* Display login failure log.
* 🍀 *New* GDPR compliance.
* 🍀 *New* Auto upgrade.
* *GUI* Setting link shortcut from plugin page.
* 🐞 Stale settings shown after successfully saved.
* 🐞 Duration setting can now be saved correctly.

= 1.0 - Sep 27 2019 =
* Initial Release.