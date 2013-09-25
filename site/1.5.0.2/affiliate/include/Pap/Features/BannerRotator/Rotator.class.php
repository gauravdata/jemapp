<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Michal Bebjak
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
class Pap_Features_BannerRotator_Rotator extends Pap_Common_Banner {
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    /**
     * @var Pap_Common_Banner_Factory
     */
    var $bannerFactory;

    var $maxRank;
    var $rotatedBanners = null;

    function __construct() {
        parent::__construct();
        $this->bannerFactory = new Pap_Common_Banner_Factory();
    }

    public function getPreview(Pap_Common_User $user) {
        $this->parseRotatorBannerDescription();
        if($this->rotatedBanners!=null){
            if(Gpf_Session::getAuthUser()->isAffiliate()) {
                return $this->getAffiliatePreview($user);
            }
            return $this->getMerchantPreview($user);
        }
        return '';
    }

    function getMerchantPreview(Pap_Common_User $user){
        $template = new Gpf_Templates_Template("rotator_preview_merchant.stpl");
        $this->addBannersForPreview($template,$user);
        return $template->getHTML();
    }

    function addBannersForPreview(Gpf_Templates_Template $template ,Pap_Common_User $user){
        $bannersPreview = '';
        $counter = 0;
        foreach ($this->rotatedBanners as $row) {
            $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
            $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
            try {
                $bannersPreview .= '<div>'. $this->bannerFactory->getBanner($bannerId)->getPreview($user).'</div><hr>';
                $counter ++;
            } catch (Gpf_Exception $e) {
            }
        }
        if ($counter >= 5) {
            $bannersPreview = '<div class="BannerRotatorPreview">' . $bannersPreview . '</div>';
        }
        $template->assign("banners",$bannersPreview);
    }

    function getAffiliatePreview(Pap_Common_User $user){
        $template = new Gpf_Templates_Template("rotator_preview_affiliate.stpl");
        $this->addBannersForPreview($template,$user);
        $template->assign("id",$this->getId());
        return $template->getHTML();
    }

    public function initValidators(Gpf_Rpc_Form $form) {
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::NAME, $this->_('name'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::STATUS, $this->_('status'));
        $form->addValidator(new Gpf_Rpc_Form_Validator_MandatoryValidator(), Pap_Db_Table_Banners::SIZE, $this->_('size'));
    }

    protected function getBannerCode(Pap_Common_User $user, $flags) {
        return '<script type="text/javascript" src="'.$this->getBannerScriptUrl($user).'"></script>';
    }

    public function getDisplayCode(Pap_Common_User $user) {
        $this->parseRotatorBannerDescription();
        $banner = $this->bannerFactory->getBanner($this->getBannerIdToShow());
        $banner->setParentBannerId($this->getId());
        return $banner->getCode($user);
    }

    public function fillCachedBanner(Pap_Db_CachedBanner $cachedBanner, Pap_Common_User $user) {
        $this->parseRotatorBannerDescription();
        $bannerIdToShow = $this->getBannerIdToShow();
        foreach ($this->rotatedBanners as $row) {
            $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
            $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
            $banner = $this->bannerFactory->getBanner($bannerId);
            $banner->setParentBannerId($this->getId());
            $this->setBannerChannel($banner, $cachedBanner->getChannel());
            if ($bannerIdToShow == $bannerId) {
                $cachedBanner->setCode($banner->getCompleteCode($user, ''));
                $cachedBanner->setRank($rank);
                $cachedBanner->setValidFrom($row->get(Pap_Db_Table_BannersInRotators::VALID_FROM));
                $cachedBanner->setValidUntil($row->get(Pap_Db_Table_BannersInRotators::VALID_UNTIL));
                continue;
            }
            $rotCachedBanner = clone $cachedBanner;
            $rotCachedBanner->setValidFrom($row->get(Pap_Db_Table_BannersInRotators::VALID_FROM));
            $rotCachedBanner->setValidUntil($row->get(Pap_Db_Table_BannersInRotators::VALID_UNTIL));
            $rotCachedBanner->setCode($banner->getCompleteCode($user, ''));
            Pap_Tracking_BannerViewer::addJavascriptCode($rotCachedBanner);
            $rotCachedBanner->setRank($rank);
            try {
                $rotCachedBanner->save();
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                // cached banner was saved already by other script
            }
        }
    }

    private function setBannerChannel(Pap_Common_Banner $banner, $channel) {
        $dbChannel = new Pap_Db_Channel();
        $dbChannel->set(Pap_Db_Table_Channels::VALUE, $channel);
        try {
            $dbChannel->loadFromData(array(Pap_Db_Table_Channels::VALUE));
            $banner->setChannel($dbChannel);
        } catch (Exception $e) {
        }
    }

    /**
     * Returns banner which should be displayed
     *
     * @return Pap_Common_Banner
     */
    private function getBannerIdToShow() {
        $sum = 0;
        $id = 0;
        list($usec, $sec) = explode(' ', microtime());
        srand((float) $sec + ((float) $usec * 100000));
        $rnd = rand(0, $this->maxRank);
        foreach ($this->rotatedBanners as $row) {
            $valid_from = $row->get(Pap_Db_Table_BannersInRotators::VALID_FROM);
            if($valid_from < date("Y-m-d H:i:s") || $valid_from == null){
                $bannerId = $row->get(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID);
                $rank = $row->get(Pap_Db_Table_BannersInRotators::RANK);
                $sum += $rank;
                if ($rnd <= $sum) {
                    return $bannerId;
                }
                $id = $bannerId;
            }
        }
        return $id;
    }

    function parseRotatorBannerDescription() {
        $row = new Pap_Db_BannerInRotator();
        $row->setParentBannerId($this->getId());
        $select = new Gpf_SqlBuilder_SelectBuilder();

        $select->select->addAll(Pap_Db_Table_BannersInRotators::getInstance());
        $select->from->add(Pap_Db_Table_BannersInRotators::getName());
        $select->where->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID,'=',$this->getId());

        $dateCondition = new Gpf_SqlBuilder_CompoundWhereCondition();
        $dateCondition->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null,'OR',false);
        $dateCondition->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date(self::DATETIME_FORMAT),'OR');

        $select->where->addCondition($dateCondition,'AND');
        foreach ($select->getAllRows() as $row){
            $this->rotatedBanners[] = $row;
            $this->maxRank+=$row->get(Pap_Db_Table_BannersInRotators::RANK);
        }
    }

    function createUpdateIncrementBuild(Pap_Common_Banner $childBanner, $column){
        $updateBuild = new Gpf_SqlBuilder_UpdateBuilder();
        $updateBuild->from->add(Pap_Db_Table_BannersInRotators::getName());
        $updateBuild->set->add($column, "$column+1", false);
        $updateBuild->where->add(Pap_Db_Table_BannersInRotators::PARENT_BANNER_ID,'=',$this->getId());
        $updateBuild->where->add(Pap_Db_Table_BannersInRotators::ROTATED_BANNER_ID,'=',$childBanner->getId());

        $c1 = new Gpf_SqlBuilder_CompoundWhereCondition();
        $c1->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'=',null);
        $c1->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null);

        $c2 = new Gpf_SqlBuilder_CompoundWhereCondition();
        $c2->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'<',date("Y-m-d H:i:s"));
        $c2->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date("Y-m-d H:i:s"));

        $c3 = new Gpf_SqlBuilder_CompoundWhereCondition();
        $c3->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'=',null);
        $c3->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'>',date("Y-m-d H:i:s"));

        $c4 = new Gpf_SqlBuilder_CompoundWhereCondition();
        $c4->add(Pap_Db_Table_BannersInRotators::VALID_FROM,'<',date("Y-m-d H:i:s"));
        $c4->add(Pap_Db_Table_BannersInRotators::VALID_UNTIL,'=',null);

        $c = new Gpf_SqlBuilder_CompoundWhereCondition();
        $c->addCondition($c1,'OR');
        $c->addCondition($c2,'OR');
        $c->addCondition($c3,'OR');
        $c->addCondition($c4,'OR');

        $updateBuild->where->addCondition($c,'AND');

        return $updateBuild;
    }

    public function saveChildImpression(Pap_Common_Banner $banner, $isUnique){
        $updateBuild = $this->createUpdateIncrementBuild($banner, Pap_Db_Table_BannersInRotators::ALL_IMPS);
        if($isUnique){
            $column = Pap_Db_Table_BannersInRotators::UNIQ_IMPS;
            $updateBuild->set->add($column, "$column+1", false);
        }
        $updateBuild->execute();
    }

    public function saveChildClick(Pap_Common_Banner $banner){
        $updateBuild = $this->createUpdateIncrementBuild($banner,  Pap_Db_Table_BannersInRotators::CLICKS);
        $updateBuild->execute();
    }
}

?>
