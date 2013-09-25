<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Maros Fric
 *   @since Version 1.0.0
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
class Pap_Tracking_Action_ComputeCommissions extends Gpf_Object implements Pap_Tracking_Common_Recognizer {

    private $recognizeCurrency;

    public function __construct() {
        $this->recognizeCurrency = new Pap_Tracking_Action_RecognizeCurrency();
    }

    public function recognize(Pap_Contexts_Tracking $context) {
        $this->computeCustomCommissions($context);
        $this->computeRealTotalCost($context);
        $this->computeFixedCost($context);
        $this->checkZeroOrdersCommissions($context);
    }

    private function checkZeroOrdersCommissions(Pap_Contexts_Tracking $context) {
        if ($context->getRealTotalCost() == 0 &&
        $context->getCommissionTypeObject()->getZeroOrdersCommissions() != Gpf::YES) {
            $context->debug("    STOPPING (setting setDoCommissionsSave(false), because TotalCost is 0 and zero order commissions should not be saved.");
            $context->setDoCommissionsSave(false);
        }
    }

    private function getParameterType($value) {
        $type = '$';
        if(strpos($value, '%') !== false) {
            $type = '%';
        }
        return $type;
    }

    private function makeCorrections($value) {
        $value = str_replace('%', '', $value);
        $value = str_replace('$', '', $value);
        $value = str_replace(',', '.', $value);
        $value = str_replace(' ', '', $value);
        return $value;
    }

    /**
     *
     * @param Pap_Contexts_Action $context
     * @return string
     */
    protected function getDefaultFixedCost($context) {
        $commissionTypeObject = $context->getCommissionTypeObject();
        if ($commissionTypeObject == null) {
            return false;
        }
        return array('fixedcosttype' => $commissionTypeObject->getFixedcostType(),
	                 'fixedcostvalue' => $commissionTypeObject->getFixedcostValue());
    }

    public function computeFixedCost(Pap_Contexts_Action $context) {
        $context->debug('Fixed cost comnputing started');
        $context->debug("    Trying to get fixed cost from request parameter '".Pap_Tracking_ActionRequest::PARAM_ACTION_FIXEDCOST."'");

        $fixedCost = $context->getFixedCostFromRequest();
        if($fixedCost == '') {
            $context->debug("    Fixedcost not found in request trying to get default for campaign.'");
            $fixedCost = $this->getDefaultFixedCost($context);
            if ($fixedCost != false) {
                $fixedCost = $fixedCost['fixedcosttype'].$fixedCost['fixedcostvalue'];
            }else{
                $fixedCost = 0;
            }
        }
        if($fixedCost != '') {
            $type = $this->getParameterType($fixedCost);
            $fixedCost = $this->makeCorrections($fixedCost);
            $value = '';
            if(is_numeric($fixedCost) && $fixedCost >= 0) {
                $value = $fixedCost;
            }
            if($value != '') {
                $context->debug("        Fixed cost is $type $value");
                if ($type == '%') {
                    if ($value > 100) {
                        $context->debug("        Fixed cost is greater than 100%!");
                        return;
                    }
                    $context->setFixedCost($context->getRealTotalCost()/100*$value);
                } elseif ($type=='$') {
                    $context->setFixedCost($value);
                    $this->recognizeCurrency->processFixedCost($context);
                }
            } else {
                $context->debug("        Fixed cost has bad format");
            }
        }else{
            $context->setFixedCost(0);
        }

        $context->debug('Fixed cost computing ended');
        $context->debug("");
    }

    private function computeRealCost(Pap_Contexts_Action $context, $valueIN, $paramName) {
        if($context->getActionType() != Pap_Common_Constants::TYPE_ACTION) {
        	$context->debug('RealCost is 0 as the transaction type is not sale/action but '.$context->getActionType());
            return 0;
        }
        $valueOriginal = $valueIN;
        $value = $this->normalizeValue($valueOriginal);

        if($valueOriginal != $value) {
            $context->debug("        $paramName value from parameter is '".$valueOriginal."', corrected to '".$value."'");
        } else {
            $context->debug("        $paramName value from parameter is '".$value."'");
        }
        if($value == ''){
            return 0;
        }
        return $value;
    }

    public function computeCustomCommissions(Pap_Contexts_Action $context) {
        $context->debug('Custom commission computing started');

        $commission = $context->getCustomCommissionFromRequest();
        if($commission != '') {
            $context->debug("        Found custom commission: ".$commission.", decoding");

            $type = $this->getParameterType($commission);
            $commission = $this->makeCorrections($commission);

            $value = '';
            if(is_numeric($commission) && $commission >= 0) {
                $value = $commission;
            }

            if($value != '') {
                $context->debug("        Custom commission is $type $value");
                $i = 1;
                while ($context->getCommission($i) != null) {
                    $context->removeCommission($i++);
                }
                $newCommission = new Pap_Tracking_Common_Commission(1, $type, $value);
                $newCommission->setStatus($this->recognizeStatus($context->getCommissionTypeObject()));
                $context->addCommission($newCommission);

                if ($type!='%'){
                    $this->recognizeCurrency->computeCustomCommission($context);
                }
            } else {
                $context->debug("        Custom commission has bad format");
            }
        } else {
        	$context->debug('No custom commission defined');
        }

        $context->debug('Checking for forced commissions ended');

        $context->debug('Custom commission computing ended');
        $context->debug("");
    }

    private function recognizeStatus(Pap_Db_CommissionType $commissionType) {
        if($commissionType->getApproval() == Pap_Db_CommissionType::APPROVAL_AUTOMATIC) {
            return Pap_Common_Constants::STATUS_APPROVED;
        }
        return Pap_Common_Constants::STATUS_PENDING;
    }

    /**
     * recomputes total cost to default currency
     *
     * @return unknown
     */
    public function computeRealTotalCost(Pap_Contexts_Tracking $context) {
        if($context->getActionType() != Pap_Common_Constants::TYPE_ACTION) {
        	$context->debug('Setting commission to 0 as the transaction type is not sale/action but '.$context->getActionType());
            return 0;
        }
        $this->recognizeCurrency->processTotalCost($context);
        $newTotalCost = $this->computeRealCost($context,$context->getRealTotalCost() ,'realTotalCost');
        $context->debug('Setting realTotalCost to '.$newTotalCost);
        $context->setRealTotalCost($newTotalCost);
    }

    /**
     * normalizes total cost, removes all spaces and non-numbers
     *
     * @param string $totalCost
     * @return string
     */
    protected function normalizeValue($value) {
        $value = str_replace('%20', '', $value);
        $value = preg_replace('/[^0-9.,\-]/', '', $value);
        return $value;
    }
}

?>
