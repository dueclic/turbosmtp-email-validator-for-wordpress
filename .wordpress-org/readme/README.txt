=== turboSMTP Email Validator ===

Contributors: dueclic
Tags: email tester, email validator, email validation
Requires at least: 6.0
Requires PHP: 7.0
Tested up to: 6.7
Stable tag: 1.6.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Email validation tool in WordPress forms registrations using turboSMTP API

== Description ==

Validates email addresses using the turboSMTP API in:
- WooCommerce registration and checkout
- Contact Form 7
- WpForms
- WordPress comments and registration
- MC4WP
- Gravity Forms
- Elementor Forms

The turboSMTP Email Validator for WordPress plugin ensures that email addresses provided during user registration and checkout are valid. It uses the turboSMTP API to perform the validation, enhancing the quality of email addresses collected and reducing the number of invalid emails in your database.

== Features ==

- Validates email addresses in different forms (WooCommerce, CF7, WpForms, WordPress comments and registration, MC4WP, Gravity Forms, Elementor Forms)
- Uses the turboSMTP API for email validation.
- Stores validated email addresses in a custom database table.
- Provides an admin settings page to configure API keys and enable/disable the service.
- Displays a list of validated email addresses in the admin area.

== Installation ==

- Upload the plugin files to the /wp-content/plugins/turbosmtp-email-validator directory, or install the plugin through the WordPress plugins screen directly.
- Activate the plugin through the 'Plugins' screen in WordPress.
- Go to "Settings" > "turboSMTP Email Validator" to configure the plugin settings.

== Usage ==

- Enable turboSMTP Email Validator: Go to "Settings" > "turboSMTP Email Validator" and check the "Enable Email Validation" option.
- Set API Keys: Enter your turboSMTP consumerKey and consumerSecret in the provided fields.
- View Validated Emails: Check the list of validated emails in the "Validated Emails" section on the settings page.

== Frequently Asked Questions ==

Q: What happens if the email validation fails during registration or checkout?

A: If the email validation fails, an error message will be displayed, and the user will be prompted to enter a valid email address.

== Screenshots ==

1. Email Validator General Settings
2. Validation Settings
3. Test Validator
4. History Table
5. History Table Validation Details
6. Login using API Keys

== Changelog ==

= 1.6.0 =
- i18n fixes

= 1.5.0 =
- Added missing translations

= 1.4.0 =
- Allow custom error message translation with WPML or Polylang

= 1.3.0 =
- Fix in threshold

= 1.2.0 =

- Added threshold for avoid multiple validations
- Test validator loading improvements
- Responsive CSS improvements in admin area

= 1.1.0 =

- Handle form_id in Elementor Forms, Contact Form 7, WordPress Comments

= 1.0.0 =

- First public release
