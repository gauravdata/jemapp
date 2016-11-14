<?php
/**
 * Created by PhpStorm.
 * User: rob
 * Date: 14-11-2016
 * Time: 12:24
 */

class Twm_Fix_Model_Observer {
    public function checkCartBefore($observer) {
        $id = session_id();
        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $sessionTable = $resource->getTableName('core/session');
        $select = $db->select()->from($sessionTable)->where('session_id = ?', $id);

        $session = $db->fetchOne($select);
        if ($id && !$session) {
            //session_regenerate_id();
            //die('renew');
        }
    }
}
