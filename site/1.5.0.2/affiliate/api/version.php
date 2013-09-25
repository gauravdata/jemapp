<?php
/**
*   @copyright Copyright (c) 2010 Quality Unit s.r.o.
*   @author Juraj Simon
*   @since Version 1.0.0
*   $Id: importLanguage.php 13163 2007-08-07 11:15:49Z aharsani $
*
*   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
*   Version 1.0 (the "License"); you may not use this file except in compliance
*   with the License. You may obtain a copy of the License at
*   http://www.qualityunit.com/licenses/license
*
*/

require_once '../scripts/bootstrap.php';

$string = <<<XML
<versionInfo>
    <applications>
        <pap>
        </pap>
    </applications>
</versionInfo>
XML;

$xml = new SimpleXMLElement($string);

$xml->applications->pap->addChild('versionNumber', Pap_Application::getInstance()->getVersion());

echo $xml->asXML();
?>
