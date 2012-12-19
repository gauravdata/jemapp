<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro 
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: TransactionsGrid.class.php 17234 2008-04-11 14:23:06Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Merchants_Tools_GettingStarted extends Gpf_Object implements Gpf_FormHandler {
    const CHECKS_COUNT = 10;
    
    /**
     * @service getting_started read
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function load(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $checks = Gpf_Settings::get(Pap_Settings::GETTING_STARTED_CHECKS);
        $checks = explode(",", $checks);
        
        for ($i=1; $i<=self::CHECKS_COUNT; $i++) {
            $form->addField("check".$i, (in_array($i, $checks)) ? GPF::YES : GPF::NO);
        }
        
        $form->addField("show", Gpf_Settings::get(Pap_Settings::GETTING_STARTED_SHOW));
        
        return $form;
    }
    
    /**
     * @service getting_started write
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function add(Gpf_Rpc_Params $params) {
        return $this->save($params);
    }
    
    /**
     * @service getting_started write
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function save(Gpf_Rpc_Params $params) {
        $form = new Gpf_Rpc_Form($params);
        $checks = array();
        
        for ($i=1; $i<=self::CHECKS_COUNT; $i++) {
            if ($form->existsField("check".$i) &&
                $form->getFieldValue("check".$i) == GPF::YES) {
                    $checks[] = $i;
                }
        }
        if (count($checks) > 0) {
            Gpf_Settings::set(Pap_Settings::GETTING_STARTED_CHECKS, implode(",", $checks));
        } else {
            Gpf_Settings::set(Pap_Settings::GETTING_STARTED_CHECKS, "");
        }
        
        Gpf_Settings::set(Pap_Settings::GETTING_STARTED_SHOW, $form->getFieldValue("show"));
        $form->setInfoMessage("");
        return $form;
    }
}
?>
