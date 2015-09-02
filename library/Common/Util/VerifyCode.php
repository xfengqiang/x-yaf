<?php
/**
 * 
 * @package Comm/Util
 * @Autor: fengqiang <fengqiang1@staff.sina.com.cn>
 * @Date: 2014-11-02 17:06
 */
namespace Common\Util;

class VerifyCode {
    const VERIFY_CODE  = 'VERIFY_CODE'; 
    
    static private function getCode($refresh, $len=4){
        $session = \Yaf_Session::getInstance();
        $code = $session->offsetGet(self::VERIFY_CODE);
        if(!$code || $refresh){
            $min = pow(10, $len-1);
            $max = pow(10, $len);
            $session->offsetSet(self::VERIFY_CODE, strval(rand($min, $max)));
        }
        return $session->offsetGet(self::VERIFY_CODE);
    }
    
    static public function isValid($num){
        $session = \Yaf_Session::getInstance();
        $code = $session->offsetGet(self::VERIFY_CODE);
        return $code && $code == $num;
    }
    
    static function createCode($num = 4, $font_size = 12, $width = 50, $height = 20) {
        $nmsg = self::getCode(true, $num);
        $im = @imagecreatetruecolor($width, $height); 
        $im or die("建立图像失败");
        imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255));
        imagerectangle($im, 0, 0, 49, 19, imagecolorallocate($im, 200, 200, 200));
        for ($i = 1; $i <= 100; $i++)
        {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $width), imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255)));
        }
        imageline($im, 0, rand(0, 5), $width, rand(14, 19), imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255)));
        imageline($im, 0, rand(14, 19), $width, rand(0, 5), imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255)));
        $x = 0;
        for($i=0;$i<strlen($nmsg);$i++)
        {
            $x = $i ===  0 ? rand(2, 5) : $x + $font_size-1 + rand(0, 1);
            $y = rand(1, 5);
            imagechar($im, $font_size, $x, $y, $nmsg[$i], imagecolorallocate($im, rand(50, 180), rand(50, 180), rand(50, 180)));
        }
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Last-Modified: Sat, 26 Jul 1997 05:00:00 GMT", true, 200 );
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
    }
} 