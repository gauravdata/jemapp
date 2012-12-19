<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: FormHandler.class.php 21318 2008-09-29 14:23:07Z mjancovic $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Rpc_FormHandler implements Gpf_Rpc_DataEncoder, Gpf_Rpc_DataDecoder {

    /**
     * @return string  string representation of input var or an error if a problem occurs
     */
    public function encodeResponse(Gpf_Rpc_Serializable $response) {
        return $response->toText();
    }

    /**
     * @param array $requestArray
     * @return StdClass
     */
    function decode($requestArray) {
        $param = new stdClass();

        $reservedParams = array(
        Gpf_Rpc_Params::CLASS_NAME,
        Gpf_Rpc_Params::METHOD_NAME,
        Gpf_Rpc_Params::SESSION_ID,
        Gpf_Rpc_Server::FORM_REQUEST,
        Gpf_Rpc_Server::FORM_RESPONSE
        );

        $recordset = new Gpf_Data_RecordSet();
        $recordset->setHeader(array("name", "value"));
        foreach ($requestArray as $name => $value) {
            if(in_array($name, $reservedParams)) {
                continue;
            }
            if(get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $record = $recordset->createRecord();
            $record->set("name", $name);
            $record->set("value", $value);
            $recordset->add($record);
        }
        $param->fields = $recordset->toObject();

        foreach ($reservedParams as $paramName) {
            if(array_key_exists($paramName, $requestArray)) {
                $param->{$paramName} = $requestArray[$paramName];
            }
        }
        return $param;
    }
}


?>
