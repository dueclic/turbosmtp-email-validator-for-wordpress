<div align="center">

[![turboSMTP](https://raw.githubusercontent.com/debba/turbosmtp-email-validator-for-woocommerce/master/.wordpress-org/assets/banner-772x250.png)
](https://www.serversmtp.com)

</div>

# turboSMTP Email Validator for WordPress

Validates email addresses using the turboSMTP API in:
- WooCommerce registration and checkout 
- Contact Form 7
- WpForms
- WordPress comments and registration
- MC4WP
- Gravity Forms
- Elementor Forms

## Description

The turboSMTP Email Validator for WordPress plugin ensures that email addresses provided during user registration and checkout are valid. It uses the turboSMTP API to perform the validation, enhancing the quality of email addresses collected and reducing the number of invalid emails in your database.

## Features

- Validates email addresses in different forms (WooCommerce, CF7, WpForms, WordPress comments and registration, MC4WP, Gravity Forms, Elementor Forms)
- Uses the turboSMTP API for email validation.
- Stores validated email addresses in a custom database table.
- Provides an admin settings page to configure API keys and enable/disable the service.
- Displays a list of validated email addresses in the admin area.
- Bypass validation for individual email addresses or entire domains using the whitelist functionality.

## Installation

- Upload the plugin files to the /wp-content/plugins/woocommerce-turbosmtp-email-validator directory, or install the plugin through the WordPress plugins screen directly.
- Activate the plugin through the 'Plugins' screen in WordPress.
- Go to "Settings" > "Email Validation" to configure the plugin settings.

## Usage

- Enable Email Validation: Go to "Settings" > "Email Validation" and check the "Enable Email Validation" option.
- Set API Keys: Enter your turboSMTP consumerKey and consumerSecret in the provided fields.
- View Validated Emails: Check the list of validated emails in the "Validated Emails" section on the settings page.

## Frequently Asked Questions

Q: What happens if the email validation fails during registration or checkout?

A: If the email validation fails, an error message will be displayed, and the user will be prompted to enter a valid email address.

Q: How often is the email validation performed?

A: The email validation is performed during user registration and checkout. If an email has been validated within the last six months, it will not be validated again.

## Screenshots

1. Email Validator Settings - General Settings
2. Email Validator Settings - Forms Configuration
3. Test Validator
4. History Table
5. Email validation details


## License

This plugin is licensed under the GPLv2 or later. See the LICENSE file for more details.
