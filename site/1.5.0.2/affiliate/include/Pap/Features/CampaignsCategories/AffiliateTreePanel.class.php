<?php
/**
*   @copyright Copyright (c) 2007 Quality Unit s.r.o.
*   @package PostAffiliatePro
*   @author Milos Jancovic
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
class Pap_Features_CampaignsCategories_AffiliateTreePanel extends Gpf_Object {    
    /**
     * @service campaigns_categories read
     * @param $fields
     */
    public function loadTree(Gpf_Rpc_Params $params) {
        $data = new Gpf_Rpc_Data($params);

        $tree = new Pap_Features_CampaignsCategories_Tree(false, array('Y'));
        $tree->laod();
        $data->setValue(Pap_Features_CampaignsCategories_TreePanel::CAMPAIGNS_CATEGORIES_TREE, $tree->toJSON());
        $data->setValue(Pap_Features_CampaignsCategories_TreePanel::CAMPAIGNS_CATEGORIES_TREE_MAXITEMCODE, '0');

        return $data;
    }
}

?>
