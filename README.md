# Xero PHP oAuth 2 App
This PHP project demonstrates how to use the xero-php-oauth2 SDK.  Use composer or clone this repository to your local machine to begin.

You'll be able to connect to a Xero Organisation, make real API calls. The code used to make each API call will be displayed along with the results returned from Xero's API.

## Getting Started
To run locally, you'll need a local web server with PHP support.  
* MAMP is a good option [Download MAMP](https://www.mamp.info/en/downloads/) 

### Download Manually
* Clone this repo into your local server webroot. 
* Launch a terminal app and change to the newly cloned folder `xero-php-oauth2-app`
* Download dependencies with Composer using the folloing comman

```
composer install
```

## Create a Xero App
To obtain your API keys, follow these steps and create a Xero app

* Create a [free Xero user account](https://www.xero.com/us/signup/api/) (if you don't have one)
* Login to [Xero developer center](https://developer.xero.com/myapps)
* Click "New App" link
* Enter your App name, company url, privacy policy url.
* Enter the redirect URI (your callback url - i.e. `http://localhost:8888/xero-php-oauth2-app/callback.php`)
* Agree to terms and condition and click "Create App".
* Click "Generate a secret" button.
* Copy your client id and client secret and save for use later.
* Click the "Save" button. You secret is now hidden.

## Configure your .env file
You'll need to setup your  `.env` file

Rename the file `sample.env` to `.env` and copy and paste your *clientId, clientSecret and redirectUri*  These .env variables will be read by authorization.php, callback.php, get.php.

Sample.env file
```bash
CLIENT_ID = "YOUR-CLIENT-ID"
CLIENT_SECRET = "YOUR-CLIENT-SECRET"
REDIRECT_URI = "http://localhost:8888/xero-php-oauth2-app/callback.php"
```

Sample PHP code from authorization.php
```php
// This library will read variable from the .env file.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$clientId = getenv('CLIENT_ID');
$clientSecret = getenv('CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
	'clientId'                => $clientId,   
	'clientSecret'            => $clientSecret,
	'redirectUri'             => $redirectUri,
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


