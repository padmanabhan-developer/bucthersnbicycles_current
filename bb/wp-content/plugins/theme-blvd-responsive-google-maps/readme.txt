=== Theme Blvd Responsive Google Maps ===
Contributors: themeblvd
Tags: themeblvd, google, map, maps, responsive
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 1.0.2

Insert a responsive Google Map with shortcode [tb_google_map].

== Description ==

= How To Use =

After activating the plugin you can use the shortcode [tb_google_map] to display a Google Map.

Example:

`[tb_google_map address="6921 Brayton Drive, Anchorage, Alaska"]`

= Shortcode Parameters =

The following is a list of all parameters you can use within the shortcode.

1. **address**: *(required)* The address to show in the map.
2. **maptype**: *(optional)* Type of map - hybrid, satellite, roadmap, or terrain.
3. **zoom**: *(optional)* Zoom level - use number 1-19.
4. **html**: *(optional)* What to show in the popup, defaults to address.
5. **popup**: *(optional)* Show or hide the popup, true or false.
6. **width**: *(optional)* Width of map, defaults to 100%. Ex: 50%, 500px, etc.
7. **height**: *(optional)* Height of map, defaults to 500px. Ex: 50%, 500px, etc.

= Credits =

This WordPress plugin utilizes the [gMap jQuery Plugin](https://github.com/marioestrada/jQuery-gMap "gMap jQuery Plugin") by Cedric Kastner and Mario Estrada.

== Installation ==

1. Upload `theme-blvd-responsive-google-maps` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode [tb_google_map].

== Screenshots ==

1. Google Map displayed in standard web browser.

2. Same Google Map from previous screenshot displayed on iPhone 3.

3. How page was setup in previous two screenshots.

== Changelog ==

= 1.0.2 =

* Fixed inclusion of Google Map API to feed directly from Google so it can't "expire".

= 1.0.1 =

* Fixed shortcode output.

= 1.0.0 =

* This is the first release.