<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package PostAffiliatePro
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *   $Id: Context.class.php 18001 2008-05-13 16:05:33Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Gpf_Common_SelectBuilderCompoundRecord {
	
	/**
	 * @var Gpf_Data_Record
	 */
    private $params;
    /**
     * @var Gpf_SqlBuilder_SelectBuilder
     */
    private $selectBuilder;

    /**
     * @param Gpf_SqlBuilder_SelectBuilder $from
     * @param Gpf_Data_Record $params
     */
    public function __construct(Gpf_SqlBuilder_SelectBuilder $selectBuilder, Gpf_Data_Record $params) {        
        $this->selectBuilder = $selectBuilder;
        $this->params = $params;
    }

    /**
     * @return Gpf_SqlBuilder_SelectBuilder
     */
    public function getSelectBuilder() {
        return $this->selectBuilder;
    }
    
    /**
     * @return Gpf_Data_Record
     */
    public function getParams() {
    	return $this->params;
    }
}
?>
