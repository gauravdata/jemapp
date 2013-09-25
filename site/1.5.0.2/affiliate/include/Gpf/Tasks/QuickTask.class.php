<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Tasks_QuickTask extends Gpf_Db_QuickTask {

    /**
     * @param $request
     * @param $groupId
     * @param $validTo
     * @return string
     */
    public function __construct(Gpf_Rpc_Request $request, Gpf_DateTime $validTo = null) {
        parent::__construct();
        $json = new Gpf_Rpc_Json();

        if ($validTo === null) {
            $validTo = new Gpf_DateTime();
            $validTo->addDay(30);
        }

        $this->setAccountId(Gpf_Session::getInstance()->getAuthUser()->getAccountId());
        $this->setGroupId('');
        $this->setRequest($json->encode($request->toObject()));
        $this->setValidTo($validTo->toDateTime());
        $this->insert();

        return $this;
    }
    
    /**
     * @return string
     */
    public function getUrl() {
        return Gpf_Paths::getInstance()->getFullScriptsUrl(). 'do.php?quicktask='.$this->getId();
    }
}

?>
