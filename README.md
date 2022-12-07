# Simple Captcha
Simple spam protector field for SilverStripe.

<img src="https://github.com/toastnz/simple-captcha/blob/main/docs/sample.png?raw=true" width="240">

## Requirements
* SilverStripe 4
* SilverStripe Spam Protection
* PHP GD

## Installation
```
composer require toastnz/simple-captcha
```

Set Simple Catcha as the default spam protector in your config.yml
```yml
SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension:
  default_spam_protector: Toast\SimpleCaptcha\Forms\SimpleCaptchaProtector
```

Enable spam protection on the form:
```php
$form->enableSpamProtection();
```
