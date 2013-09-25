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
class Pap_Features_BannerRotator_Main extends Gpf_Plugins_Handler {

    public static function getHandlerInstance() {
        return new Pap_Features_BannerRotator_Main();
    }

    public function getStatsSelect(Pap_Stats_StatsSelectContext $statsSelectContext) {
        if ($statsSelectContext->getGroupColumn() == Pap_Db_Table_Banners::ID) {
            $select = new Gpf_SqlBuilder_SelectBuilder();
            $select->cloneObj($statsSelectContext->getSelectBuilder());
            $select->select->replaceColumn($statsSelectContext->getGroupColumnAlias(), Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID, $statsSelectContext->getGroupColumnAlias());
            $select->groupBy->removeByName(Pap_Db_Table_Banners::ID);
            $select->groupBy->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID);
            $statsSelectContext->getUnionBuilder()->addSelect($select);
        }
    }
}

?>
