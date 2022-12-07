<?php
namespace Toast\SimpleCaptcha\Forms;

use SilverStripe\SpamProtection\SpamProtector;

class SimpleCaptchaProtector implements SpamProtector {

    public function getFormField($name = 'SCaptchaField', $title = 'Captcha', $value = null) 
    {
        return SimpleCaptchaField::create($name, $title);
    }
    
    public function setFieldMapping($fieldMapping)
    {
    }
}
