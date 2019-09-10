<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 17-1-2018
 * Time: 14:51
 */ 
class Twm_CustomRewrite_Model_Core_Session_Abstract_Varien extends Mage_Core_Model_Session_Abstract_Varien
{
    public function isBot() {
        $isbot = false;
        $bot_regex = '/^alexa|^blitz\.io|bot|^browsermob|crawl|^curl|^facebookexternalhit|feed|google web preview|^ia_archiver|^java|jakarta|^load impact|^magespeedtest|monitor|nagios|^pinterest|postrank|slurp|spider|uptime|yandex/i';
        $userAgent = empty($_SERVER['HTTP_USER_AGENT']) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
        $isBot = ! $userAgent || preg_match($bot_regex, $userAgent);
        return $isBot;
    }

    public function start($sessionName=null)
    {
        if ($this->isBot()) {
            return false;
        } // Don't start session if visitor is a bot.
        return parent::start($sessionName);
    }
}