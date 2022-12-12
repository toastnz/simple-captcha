<?php
namespace Toast\SimpleCaptcha\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class SimpleCaptchaField extends FormField 
{
    use Injectable;
    use Configurable;
   
    private static $challenge_characters;
    
    private static $challenge_length;

    private static $font_size;

    private static $ignore_case;

    private static $validation_error_message;

    protected $challengeSessionName = 'SimpleCaptchaField_Challenge';


    public function __construct($name, $title = null, $value = null)
    {
        $this->setAttribute('maxlength', 6);
        parent::__construct($name, $title, $value);
    }

    public function Field($properties = []) 
    {
        return parent::Field($properties);
    }
    
    public function validate($validator) 
    {
        $session = Controller::curr()->getRequest()->getSession();
        $captcha = $session->get($this->challengeSessionName);
        $captchaResponse = Controller::curr()->getRequest()->requestVar($this->getName());        
        $session->clear($this->challengeSessionName);

        if ($this->config()->ignore_case) {
            $captcha = strtolower($captcha);
            $captchaResponse = strtolower($captchaResponse);
        }

        if (!$captchaResponse || !$captcha || ($captcha != $captchaResponse)) {
            $validator->validationError($this->name, $this->config()->validation_error_message);
            return false;
        }

        return true;
    }

    public function getChallengeImage()
    {
        $captcha = $this->getUID($this->config()->challenge_length, $this->config()->challenge_characters);
        Controller::curr()->getRequest()->getSession()->set($this->challengeSessionName, $captcha);

        $width = 200;
        $height = 60;

        // create image
        $image = imagecreatetruecolor($width, $height); 
         
        // background colour
        $bg = imagecolorallocate($image, 240, 240, 240);
        imagefill($image, 0, 0, $bg);

        // background lines
        imagefilledrectangle($image, 0, 0, 200, 50, $bg);

        for($i=0; $i < 10; $i++) {
            $lineColor = imagecolorallocatealpha($image, rand(0, 195), rand(0, 195), rand(0, 195), rand(60, 80)); 
            imageline($image, 0, rand()%100, $width, rand()%50, $lineColor);
        }        

        // text
        for($c = 0; $c < strlen($captcha); $c++) {
            $color = imagecolorallocate($image, rand(0, 195), rand(0, 195), rand(0, 195));
            $fontSize = $this->config()->font_size == 'auto' ? rand(18, 25) : $this->config()->font_size;
            imagettftext($image, $fontSize, rand(-30, 30), 10 + ($c * 30), rand(30, 50), $color, __DIR__ . '/../../fonts/arial.ttf', $captcha[$c]);
        }

        // output
        ob_start();
        imagepng($image);
        $imageContents = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);        

        return 'data:image/png;base64,' . base64_encode($imageContents);
    }


    private function getUID($size = 6, $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789')
    {
        $uid = '';
        $str = $chars;
        for ($i = 0; $i < $size; $i++) {
            $uid .= substr($str, rand(0, strlen($str)), 1);
        }
        return $uid;
    }

}