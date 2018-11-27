<?php

require_once 'abstract.php';

class Lesti_Fpc_Shell_Clean extends Mage_Shell_Abstract
{
    public function run()
    {
        Mage::getSingleton('fpc/fpc')->clean();
    }
}

$shell = new Lesti_Fpc_Shell_Clean();
$shell->run();