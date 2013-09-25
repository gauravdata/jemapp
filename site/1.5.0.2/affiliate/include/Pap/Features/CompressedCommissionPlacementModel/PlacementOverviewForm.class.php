<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Matej Kendera
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
class Pap_Features_CompressedCommissionPlacementModel_PlacementOverviewForm extends Gpf_Object {

    /**
     * @service compressed_commission_placement_model read
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function simulation(Gpf_Rpc_Params $params) {
        return $this->action($params, $this->_('It is not possible to simulate recalculation commissions of affiliates who reached condition.'), true);
    }

    /**
     * @service compressed_commission_placement_model write
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Form
     */
    public function recalculate(Gpf_Rpc_Params $params) {
        return $this->action($params, $this->_('It is not possible to recalculate commissions of affiliates who reached condition.'));
    }

    /**
     *
     * @param $message
     * @param $simulation
     * @return Gpf_Rpc_Form
     */
    private function action(Gpf_Rpc_Params $params, $message = '', $simulation = false) {
        $data = new Gpf_Rpc_Data($params);

        $filters = new Gpf_Rpc_FilterCollection($params);
        if ($filters->getFilterValue('reachedCondition') == Gpf::YES) {
            $data->setValue('message', $message);
            return $data;
        }

        $compressedCommissionProcesor = new Pap_Features_CompressedCommissionPlacementModel_Processor();
        $output = $compressedCommissionProcesor->recalculate($params->get('filters'), $simulation);

        $data->setValue('message', $output);
        return $data;
    }

}
?>
