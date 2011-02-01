<?php

abstract class Model_Abstract {

    function __construct ($properties = null)
    {
        if (is_array($properties) && count($properties)>0) {
            $this->setProperties($properties);
        }
    }
    
    protected function setProperties(array $properties = array())
    {
        foreach ($properties as $name => $value)
        {
            $method = "set";
            $parts = explode('_',$name);
            foreach($parts as $name)
            {
                $method .= ucfirst($name);
            }
            if (method_exists($this, $method))
            {
                call_user_func(array($this, $method), $value);    
            }
        }
    }
    
    public function _getProperties()
    {
        return get_object_vars($this);
    }
}