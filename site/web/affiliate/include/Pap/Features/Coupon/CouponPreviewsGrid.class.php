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
class Pap_Features_Coupon_CouponPreviewsGrid extends Pap_Features_Coupon_CouponsGrid implements Gpf_View_Grid_HasRowFilter {

    private $decreaseCount = 0;

    /**
     * @var Pap_Features_Coupon_Coupon
     */
    private $banner;

    /**
     * @service coupon read
     * @return Gpf_Rpc_Serializable
     */
    public function getRows(Gpf_Rpc_Params $params) {
        $params->add('limit', 5);
        return parent::getRows($params);
    }

    protected function initViewColumns() {
        $this->addViewColumn('coupon', $this->_("Coupon"), true);
        $this->addViewColumn('validity', $this->_("Validity"));
    }

    protected function initDataColumns() {
        $this->setKeyDataColumn('c.'.Pap_Db_Table_Coupons::ID);
        $this->addDataColumn('code', 'c.'.Pap_Db_Table_Coupons::CODE);
        $this->addDataColumn('status', 'c.'.Pap_Db_Table_Coupons::STATUS);
        $this->addDataColumn('valid_from', 'c.'.Pap_Db_Table_Coupons::VALID_FROM);
        $this->addDataColumn('valid_to', 'c.'.Pap_Db_Table_Coupons::VALID_TO);
        $this->addDataColumn('limit_use', 'c.'.Pap_Db_Table_Coupons::MAX_USE_COUNT);
        $this->addDataColumn('sales', 'COUNT(t.'.Pap_Db_Table_Transactions::TRANSACTION_ID.')');
    }

    protected function initDefaultView() {
        $this->addDefaultViewColumn('coupon', '400', 'Y');
        $this->addDefaultViewColumn('validity', '150');
    }

    protected function buildFrom() {
        $this->_selectBuilder->from->add(Pap_Db_Table_Coupons::getName(), 'c');
        $this->_selectBuilder->from->addInnerJoin(Pap_Db_Table_Users::getName(),
            'pu', 'pu.'.Pap_Db_Table_Users::ID.'=c.'.Pap_Db_Table_Coupons::USERID);
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_Users::getName(),
            'gu', 'pu.'.Pap_Db_Table_Users::ACCOUNTUSERID.'=gu.'.Gpf_Db_Table_Users::ID);
        $this->_selectBuilder->from->addInnerJoin(Gpf_Db_Table_AuthUsers::getName(),
            'au', 'au.'.Gpf_Db_Table_AuthUsers::ID.'=gu.'.Gpf_Db_Table_Users::AUTHID);
        $this->_selectBuilder->from->addLeftJoin(Pap_Db_Table_Transactions::getName(),
            't', 'c.'.Pap_Db_Table_Coupons::ID.'=t.'.Pap_Db_Table_Transactions::COUPON_ID.' and t.'.Pap_Db_Table_Transactions::TIER.'= 1'); 
    }

    protected function buildWhere() {
        parent::buildWhere();
        $this->_selectBuilder->where->add('c.'.Pap_Db_Table_Coupons::USERID, '=', Gpf_Session::getAuthUser()->getPapUserId());
    }

    /**
     * @return Gpf_Data_RecordSet
     */
    protected function initResult() {
        $result = parent::initResult();
        $result->addColumn('coupon', '');
        $result->addColumn('validity', '');
        return $result;
    }

    public function filterRow(Gpf_Data_Row $row) {
    	$row->set('valid_from', $this->createDateTime($row->get('valid_from'))->toLocaleDate());        
        $row->set('valid_to', $this->createDateTime($row->get('valid_to'))->toLocaleDate());
        $this->replaceConstants($row);
        if (!$row->get('validity')) {
            $this->decreaseCount++;
            return null;
        }
        return $row;
    }

    protected function createResultSelect() {
        parent::createResultSelect();
        $this->banner = new Pap_Features_Coupon_Coupon();
        $this->banner->setPrimaryKeyValue($this->getBannerID());
        try {
            $this->banner->load();
        } catch (Gpf_DbEngine_NoRowException $e) {
            throw new Gpf_Exception($this->_('Coupon banner with id %s not exist', $this->getBannerID()));
        }
    }

    protected function getCount(){
        return $this->_count - $this->decreaseCount;
    }

    private function replaceConstants(Gpf_Data_Record $row) {
        $coupon = new Pap_Db_Coupon();
        $coupon->setId($row->get('id'));
        $coupon->setCode($row->get('code'));
        $coupon->setStatus($row->get('status'));
        $coupon->set(Pap_Db_Table_Coupons::MAX_USE_COUNT, $row->get('limit_use'));
        $coupon->set(Pap_Db_Table_Coupons::VALID_FROM, $row->get('valid_from'));
        $coupon->set(Pap_Db_Table_Coupons::VALID_TO, $row->get('valid_to'));
        $row->add('coupon', $this->banner->getCouponText($coupon));                
        $row->add('validity', $coupon->isValid());
    }
    
    /**
     * @param DateTime String $time
     * @return Gpf_DateTime
     */
    private function createDateTime($time) {
    	return new Gpf_DateTime($time);
    }
}
?>
