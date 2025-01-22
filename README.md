[![turboSMTP](https://raw.githubusercontent.com/debba/turbosmtp-email-validator-for-woocommerce/master/.wordpress-org/assets/banner.png)
](https://www.serversmtp.com)

# turboSMTP Email Validator for WooCommerce

Validates email addresses during WooCommerce registration and checkout using the turboSMTP API.

## Description

The WooCommerce turboSMTP Email Validator plugin ensures that email addresses provided during user registration and checkout are valid. It uses the turboSMTP API to perform the validation, enhancing the quality of email addresses collected and reducing the number of invalid emails in your database.

## Features

- Validates email addresses during WooCommerce user registration.
- Validates email addresses during WooCommerce checkout.
- Uses the turboSMTP API for email validation.
- Stores validated email addresses in a custom database table.
- Provides an admin settings page to configure API keys and enable/disable the service.
- Displays a list of validated email addresses in the admin area.

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

[![Admin Page](https://raw.githubusercontent.com/debba/turbosmtp-email-validator-for-woocommerce/master/.wordpress-org/assets/screenshot-1.png)
](https://raw.githubusercontent.com/debba/turbosmtp-email-validator-for-woocommerce/master/.wordpress-org/assets/screenshot-1.png)

## License

This plugin is licensed under the GPLv2 or later. See the LICENSE file for more details.
