<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
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
class Gpf_Install_IntegrityCheck extends Gpf_Install_Step {
    
    public function __construct() {
        parent::__construct();
        $this->code = 'Integrity-Check';
        $this->name = $this->_('Integrity check'); 
    }
    
    /**
     *
     * @param Gpf_Rpc_Params $params
     * @return Gpf_Rpc_Serializable
     * @service
     * @anonym
     */
    public function load(Gpf_Rpc_Params $params) {
        return new Gpf_Rpc_Form($params);
    }
        
    protected function execute(Gpf_Rpc_Params $params) {
        return new Gpf_Rpc_Form($params);
    }
}
?>
