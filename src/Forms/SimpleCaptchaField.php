<?php
namespace Toast\SimpleCaptcha\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;

class SimpleCaptchaField extends FormField 
{
    use Configurable;

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

        if (!$captchaResponse || !$captcha || ($captcha != $captchaResponse)) {
            $validator->validationError($this->name, 'Invalid captcha response. Please try again.');
            return false;
        }

        return true;
    }

    public function getChallengeImage()
    {
        $captcha = $this->getUID(6);
        Controller::curr()->getRequest()->getSession()->set($this->challengeSessionName, $captcha);

        $width = 200;
        $height = 60;

        // create image
        $image = imagecreatetruecolor($width, $height); 
         
        // background colour
        $bg = imagecolorallocate($image, 255, 255, 255);
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
            imagettftext($image, rand(18, 25), rand(-30, 30), 10 + ($c * 30), rand(30, 50), $color, __DIR__ . '/../../fonts/arial.ttf', $captcha[$c]);
        }

        // output
        ob_start();
        imagepng($image);
        $imageContents = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);        

        return 'data:image/png;base64,' . base64_encode($imageContents);
    }


    private function getUID($size = 6)
    {
        $uid = '';
        $str = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789';
        for ($i = 0; $i < $size; $i++) {
            $uid .= substr($str, rand(0, strlen($str)), 1);
        }
        return $uid;
    }

}