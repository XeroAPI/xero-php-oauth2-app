# Xero PHP oAuth 2 App
This java project demonstrates how to use the Xero-Java SDK.  Clone this repository to your local machine to begin.

This is a project is for use with xero-php-oauth2 SDK. This app demonstrates the functionality of Xero accounting API endpoints and their related actions. 

You'll be able to connect to a Xero Organisation, make real API calls. The code used to make each API call will be displayed along with the results returned from Xero's API.

## Getting Started
To run locally, you'll need a local web server with PHP support.  
* MAMP is a good option [Download MAMP](https://www.mamp.info/en/downloads/) 

Follow these steps
* Clone this repo into your webroot. Look in your MAMP folder for `htdocs`
* Launch a terminal app and change to the newly cloned folder `xero-php-oauth2-app`
* Download dependencies with Composer.

`composer init`

New to Composer? You'll want to [install Composer](https://getcomposer.org/doc/00-intro.md)

## Create a Xero App
To obtain your API keys, follow these steps and create a Xero app

* Create a [free Xero user account](https://www.xero.com/us/signup/api/) (if you don't have one)
* Login to [Xero developer center](https://developer.xero.com/myapps)
* Click "Try oAuth2" link
* Enter your App name, company url, privacy policy url.
* Enter the redirect URI (this is your callback url - localhost, etc)
* Agree to terms and condition and click "Create App".
* Click "Generate a secret" button.
* Copy your client id and client secret and save for use later.
* Click the "Save" button. You secret is now hidden.

## Add your API keys to this app
You'll need to set the *clientId, clientSecret and redirectUri* in the following files

* authorization.php
* callback.php
* get.php.

```php
	$provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => '__YOUR_CLIENT_ID__',   
        'clientSecret'            => '__YOUR_CLIENT_SECRET__',
        'redirectUri'             => 'http://localhost:8888/xero-php-oauth2-app/callback.php',
	    'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
	    'urlAccessToken'          => 'https://identity.xero.com/connect/token',
	    'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
	]);
```

## License

This software is published under the [MIT License](http://en.wikipedia.org/wiki/MIT_License).

	Copyright (c) 2019 Xero Limited

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.


