=== Backdrop ===
Contributors: Phillip.Gooch
Tags: background, customization, themes
Requires at least: 4.0
Tested up to: 4.2.1
Stable tag: 2.1.2
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Backdrop is an improved site background customizer allowing for all manner of fancy things.

== Description ==

Backdrop is a site background customizer with power greater than the mere stock background selector included with WordPress. What can backdrop do that the default can't? I'm glad you asked...

+ More than just boring backgrounds, flexible backgrounds, parallax backgrounds, even sliding backgrounds are stuffed in here too (don't worry, the boring ones are still around as well).
+ Options, options, options! More options that you can shake a well sharpened stick at.
+ Uses the WordPress customizer interface so you can see your changes as you make them.
+ One click HiDpi (or retina) backgrounds, make it look all purdy on that fancy screen.
+ Theme schmeme, we don't need support in your stinkin theme, backdrop can force it's way into nearly any theme.
+ Calls external JS and CSS files that are appropriately cached.
+ Advanced options for those in the know with CSS.
+ Built for the smoothest animations possible.

_Note: Starting with version 2.0 Backdrop will no longer work it's magic with older versions of Internet Explorer (before version 10). IF you want to support animations on older versions of IE then please use Backdrop 1.X._

__Note: Your Backdrop settings will not be transfered from 1.X to 2.X, you will need to re-set your backdrop on update.__

== Upgrade Notice ==

__Note: Your Backdrop settings will not be transfered from 1.X to 2.X, you will need to re-set your backdrop on update.__

== Installation ==

1. Upload the `backdrop` directory to your `/wp-content/plugins/` directory.

== Frequently Asked Questions ==

= I got this great idea, can you implement it? = 

Probably, let me know and I'll see if I can work it in there.

== Screenshots ==

1. The backdrop WordPress customizer panel.

== Changelog ==

#### 2.1.3
 + Fixed a possible Notice error when in the customizer.
 + Check compatibility with WordPress 4.2.1
#### 2.1.2
 + Changes some of the session logic to support versions of PHP pre 5.4.
 + Fixed an order error regarding headers in the generator file.
 + Added a second check for adding the #backdrop-element to prevent odd cases where it would not load even though it should.
#### 2.1.1
 + Fixed a bug that would cause the live preview not to update until you saved.
 + Added an option to not include the default backdrop element when in advanced mode.
 + Added links to the generated CSS and JS files when in advanced mode.
 + Added additional debug information to CSS and JS files when in advanced mode.
 + Plugin now checks to ensure sessions are available before opening one in order to prevent possible errors.
 + Fixed a possible CSS parse error if there was no background image used.
#### 2.0.0
 + New from the ground up backdrop now supports the WP customizer.
 + Improvements to how the style-sheet and javascript files are generated.
 + Faster animations for smoother scrolling.
 + Added background color opacity option.
#### 1.3.6
 + Confirmed plugin compatibility with WordPress 4.1
#### 1.3.5
 + Confirmed plugin compatibility with WordPress 4.0
#### 1.3.4
 + Added the ability to set the horizontal alignment to “stretch”, makign it exapnd to full width on some image background attachment types.
 + Fixed a JavasScript quirk that would cause the background to jump to where it should be on page reload.
#### 1.2.3
 + Fixed a bug where the readme did not properly update for version 1.1.2
 + Added a new “Miscellaneous” section to the settings page.
 + Added a proper screenshot to replace the one that went missing at some point.
#### 1.1.2
 + Added options to control horizontal position when using parallax.
#### 1.0.2
 + Fixed an issue that would cause the CSS and JS files generated to be replaced with the default blank ones when the plugin was updated.
#### 1.0.1
 + Fixed a broken image link on settings page.
 + Removed an unused variable from the default settings and it's appropriate settings page option.
#### 1.0.0
 + Initial Release