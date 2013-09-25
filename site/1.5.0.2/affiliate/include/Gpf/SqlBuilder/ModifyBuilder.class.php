<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: ModifyBuilder.class.php 18000 2008-05-13 16:00:48Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 *   @package GwtPhpFramework
 */
abstract class Gpf_SqlBuilder_ModifyBuilder extends Gpf_Object {
    /**
     *
     * @return Gpf_DbEngine_Driver_Mysql_Statement
     */
    public function execute() {
        return $this->createDatabase()->execute($this->toString());
    }

    public function executeOne() {
        $statement = $this->execute();
        $affectedRows = $statement->affectedRows();
        if ($affectedRows > 1) {
            throw new Gpf_DbEngine_TooManyRowsException($this);
        }
    }
}

?>
