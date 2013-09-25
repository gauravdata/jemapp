<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: InfoAuthToken.class.php 22623 2008-12-02 15:39:31Z mbebjak $
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
class Gpf_Auth_InfoAuthToken extends Gpf_Auth_Info {
    private $authToken;
       
    public function __construct($accountId, $authToken = '', $roleType = '') {
        parent::__construct($accountId, $roleType);
        $this->authToken = $authToken;
        if($authToken == '') {
            $this->authToken = Gpf_Session::getAuthUser()->getRemeberMeToken();        
        }
    }
    
    public function addWhere(Gpf_SqlBuilder_SelectBuilder $builder) {
        parent::addWhere($builder);
        $builder->where->add('au.authtoken', '=', $this->authToken);
    }
}
?>
