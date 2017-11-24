<?php

/**
 * @link https://github.com/SeanJA/countdown-clock
 * Class Twm_Countdown_IndexController
 */
class Twm_Countdown_IndexController extends Mage_Core_Controller_Front_Action
{
    const FONT = '/frontend/twm/default/dist/fonts/Raleway-Bold.ttf';
    const BACKGROUND = 'assets/countdown.png';

    public function indexAction()
    {
        if ($this->getRequest()->getParam('date'))
        {
            $myRoot = Mage::getBaseDir('code') . '/local/Twm/Countdown/';

            date_default_timezone_set('Europe/Amsterdam');
            include $myRoot . 'vendor/SeanJA/countdown-clock/GIFEncoder.class.php';

            $future_date = new DateTime($this->getRequest()->getParam('date'));

            $now = new DateTime();

            $frames = [];
            $delays = [];

            $image = imagecreatefrompng($myRoot . self::BACKGROUND);
            $delay = 100; // milliseconds
            $font = [
                'size'     => 40,
                'angle'    => 0,
                'x-offset' => 103,
                'y-offset' => 60,
                'file'     => Mage::getBaseDir('skin') . self::FONT,
                'color'    => imagecolorallocate($image, 0, 0, 0)
            ];

            for ($i = 0; $i <= 60; $i++)
            {
                $interval = date_diff($future_date, $now);
                if ($future_date < $now)
                {
                    // Open the first source image and add the text.
                    $image = imagecreatefrompng($myRoot . self::BACKGROUND);
                    $text = $interval->format('00:00:00:00');
                    imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
                    ob_start();
                    imagegif($image);
                    $frames[] = ob_get_contents();
                    $delays[] = $delay;
                    $loops = 1;
                    ob_end_clean();
                    break;
                }
                else
                {
                    // Open the first source image and add the text.
                    $image = imagecreatefrompng($myRoot . self::BACKGROUND);
                    $text = $interval->format('%a:%H:%I:%S');
                    // %a is weird in that it doesn’t give you a two digit number
                    // check if it starts with a single digit 0-9
                    // and prepend a 0 if it does
                    if (preg_match('/^[0-9]\:/', $text))
                    {
                        $text = '0' . $text;
                    }

                    imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
                    ob_start();
                    imagegif($image);
                    $frames[] = ob_get_contents();
                    $delays[] = $delay;
                    $loops = 0;
                    ob_end_clean();

                }
                $now->modify('+1 second');
            }

            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            $gif = new AnimatedGif($frames, $delays, $loops);
            $gif->display();
        }
        else
            return $this->getResponse()->setHttpResponseCode(404);
    }

}
