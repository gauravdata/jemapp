<?php

/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: Url.class.php 21629 2008-10-16 09:44:43Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

class Gpf_Ui_Controller_Url extends Gpf_Object {
    private $path = array();
    private $fragmet = '';
    private $query = '';
    private $url;

    public function __construct($url = '') {
        $this->url = $url;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    public function setPathString($path) {
        $this->pathString = $path;
    }

    public function getPathString() {
        return $this->pathString;
    }

    public function setFragment($fragment) {
        $this->fragmet = $fragment;
    }

    public function addPath($path) {
        $this->path[] = $path;
    }

    public function getPaths() {
        return $this->path;
    }

    public function getPath($index) {
        if(!array_key_exists($index, $this->path)) {
            return '';
        }
        return $this->path[$index];
    }
    
    public function getLastPath() {
        if (count($this->path) > 0) {
            return $this->path[count($this->path) - 1];
        }
        return '';
    }

    public function getQuery() {
        return $this->query;
    }
    
    public function getFragment() {
        return $this->fragmet;
    }
    
    public function getUrl() {
        return $this->url;
    }
}
