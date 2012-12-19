<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: UrlParser.class.php 21629 2008-10-16 09:44:43Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Gpf_Ui_Controller_UrlParser extends Gpf_Object {
    /**
     *
     * @var Gpf_Ui_Controller_Url
     */
    private $url;

    /**
     *
     * @param string $url
     * @return Gpf_Ui_Controller_Url
     */
    public function parse($url) {
        $this->url = new Gpf_Ui_Controller_Url($url);

        $parsedUrl = parse_url('http://localhost/' . $url);
        
        if(isset($parsedUrl['query'])) {
            $this->url->setQuery($parsedUrl['query']);
        }

        if(isset($parsedUrl['fragment'])) {
            $this->url->setFragment($parsedUrl['fragment']);
        }
        
        $this->parsePath($parsedUrl);
        return $this->url;
    }
    
    public function getUrlPath(Gpf_Ui_Controller_Url $url) {
        return $url->getPathString();
    }
    
    public function getUrlQuery(Gpf_Ui_Controller_Url $url) {
        $string = '';
        if(strlen($url->getQuery())) {
            $string = $url->getQuery();
        }
        if(strlen($url->getFragment())) {
            $string .= '#' . $url->getFragment();
        }
        return $string;
    }
    
    public function toString(Gpf_Ui_Controller_Url $url) {
        $string = $this->getUrlPath($url);
        $query = $this->getUrlQuery($url);
        if($query) {
            $string .= '?' . $query;
        }
        return $string;
    }
    
    private function parsePath($parsedUrl) {
        if(!isset($parsedUrl['path'])) {
            return;
        }
        $relativeParts = explode('/', trim($parsedUrl['path'], '/'));

        foreach ($relativeParts as $part) {
            if(strlen(trim($part))) {
                $this->url->addPath(trim($part));
            }
        }
    }
}
