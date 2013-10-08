<?php
/**
 * Emalo Import Module
  */
class Icreators_Emalo_Model_Soap_Api_V2 extends Icreators_Emalo_Model_Soap_Api
{
    /**
     * Prepare data to insert/update.
     * Creating array for stdClass Object
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_prepareData($_data);
        }
        return array();
    }


}
