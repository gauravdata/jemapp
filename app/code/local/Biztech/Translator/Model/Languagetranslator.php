<?php
class Biztech_Translator_Model_Languagetranslator
{
    const ENDPOINT = 'https://www.googleapis.com/language/translate/v2';
    protected $_apiKey;
    public function __construct($apiKey)
    {
        $this->_apiKey = $apiKey;
    }
    public function translate($data, $target, $source = '')
    {
        $values = array(
            'key'    => $this->_apiKey,
            'target' => $target,
            'q'      => $data
            );

        if (strlen($source) > 0) {
            $values['source'] = $source;
        }

        $formData = http_build_query($values);
        $ch = curl_init(self::ENDPOINT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, Mage::getBaseUrl());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
        $json = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($json, true);
        return $data;            
    }
}